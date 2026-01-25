import 'dart:async';
import 'package:flutter/material.dart';
import 'package:webview_flutter/webview_flutter.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../providers/app_provider.dart';
import '../providers/language_provider.dart';
import '../services/google_ad_service.dart';
import '../widgets/toast/professional_toast.dart';
import '../widgets/coin_animation_overlay.dart';
import 'package:share_plus/share_plus.dart';
import 'package:flutter/services.dart';
import '../utils/app_theme.dart';

class GameWebViewScreen extends StatefulWidget {
  final String url;
  final String title;
  final int rewardAmount;
  final int durationSeconds;

  const GameWebViewScreen({
    super.key,
    required this.url,
    required this.title,
    this.rewardAmount = 50,
    this.durationSeconds = 60,
  });

  @override
  State<GameWebViewScreen> createState() => _GameWebViewScreenState();
}

class _GameWebViewScreenState extends State<GameWebViewScreen> {
  late final WebViewController _controller;
  Timer? _timer;
  int _elapsedSeconds = 0;
  bool _isRewardClaimed = false;
  bool _isTimerComplete = false;
  double _progress = 0.0;
  final GlobalKey _coinIconKey = GlobalKey();

  @override
  void initState() {
    super.initState();
    _initWebView();
    _startTimer();
  }

  void _initWebView() {
    _controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setBackgroundColor(const Color(0x00000000))
      ..setNavigationDelegate(
        NavigationDelegate(
          onProgress: (int progress) {
            if (mounted) {
              setState(() {
                _progress = progress / 100;
              });
            }
          },
          onPageStarted: (String url) {},
          onPageFinished: (String url) {},
          onWebResourceError: (WebResourceError error) {},
        ),
      )
      ..loadRequest(Uri.parse(widget.url));
  }

  void _startTimer() {
    _timer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (!mounted) return;
      setState(() {
        _elapsedSeconds++;
        if (_elapsedSeconds >= widget.durationSeconds && !_isTimerComplete) {
          _isTimerComplete = true;
          // Notify user slightly that they can now exit
          ProfessionalToast.showSuccess(context, message: Provider.of<LanguageProvider>(context, listen: false).getText('goal_reached_msg'));
        }
      });
    });
  }

  Future<void> _handleExit() async {
    // Always exit silently and pass data back to the previous screen
    // The previous screen (TaskScreen/AllGamesScreen) will handle showing Warning or Reward dialogs
    Navigator.pop(context, {
      'playedSeconds': _elapsedSeconds,
      'rewardClaimed': _isRewardClaimed,
      'isTimerComplete': _isTimerComplete,
    });
  }

  Future<bool> _onWillPop() async {
    await _handleExit();
    return false; // Prevent system back, we handled it manually
  }

  @override
  void dispose() {
    _timer?.cancel();
    super.dispose();
  }

  String _getDomain(String url) {
    try {
      final uri = Uri.parse(url);
      return uri.host;
    } catch (e) {
      return url;
    }
  }

  @override
  Widget build(BuildContext context) {
    return WillPopScope(
      onWillPop: _onWillPop,
      child: Scaffold(
        backgroundColor: AppTheme.darkBackgroundColor,
        body: SafeArea(
          child: Stack(
            children: [
              // WebView
              Padding(
                padding: const EdgeInsets.only(top: 56), // Space for top bar
                child: WebViewWidget(controller: _controller),
              ),

              // Loading Bar
              if (_progress < 1.0)
                Positioned(
                  top: 56,
                  left: 0,
                  right: 0,
                  child: LinearProgressIndicator(
                    value: _progress,
                    color: Colors.blueAccent,
                    backgroundColor: Colors.transparent,
                    minHeight: 2,
                  ),
                ),

              // Chrome-style Top Bar
              Positioned(
                top: 0,
                left: 0,
                right: 0,
                height: 56,
                child: Container(
                  decoration: AppTheme.backgroundDecoration,
                  padding: const EdgeInsets.symmetric(horizontal: 8),
                  child: Row(
                    children: [
                      // Close Button (X)
                      IconButton(
                        icon: const Icon(Icons.close, color: Colors.white, size: 24),
                        onPressed: () {
                          _handleExit();
                        },
                      ),
                      
                      const SizedBox(width: 8),

                      // URL/Title Area
                      Expanded(
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              widget.title,
                              style: GoogleFonts.roboto(
                                color: Colors.white,
                                fontSize: 16,
                                fontWeight: FontWeight.w500,
                              ),
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                            ),
                            Row(
                              children: [
                                const Icon(Icons.lock, size: 12, color: Colors.green),
                                const SizedBox(width: 4),
                                Expanded(
                                  child: Text(
                                    _getDomain(widget.url),
                                    style: GoogleFonts.roboto(
                                      color: Colors.white70,
                                      fontSize: 12,
                                    ),
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                  ),
                                ),
                              ],
                            ),
                          ],
                        ),
                      ),

                      // Timer (Subtle Badge)
                      if (!_isTimerComplete)
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                          decoration: BoxDecoration(
                            color: Colors.white.withOpacity(0.1),
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(color: Colors.white24),
                          ),
                          child: Row(
                            children: [
                              const Icon(Icons.timer, color: Colors.white70, size: 14),
                              const SizedBox(width: 4),
                              Text(
                                "${widget.durationSeconds - _elapsedSeconds}${Provider.of<LanguageProvider>(context).getText('seconds_short')}",
                                style: GoogleFonts.roboto(
                                  color: Colors.black87,
                                  fontSize: 12,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ],
                          ),
                        )
                      else
                         const Icon(Icons.check_circle, color: Colors.green, size: 20),

                      const SizedBox(width: 8),

                      // Share Button
                      IconButton(
                        icon: const Icon(Icons.share_outlined, color: Colors.black87, size: 22),
                        onPressed: () {
                          Share.share(widget.url);
                        },
                      ),

                      // Menu Button (Visual only for now)
                      IconButton(
                        icon: const Icon(Icons.more_vert, color: Colors.black87, size: 24),
                        onPressed: () {
                          // Show menu if needed, for now just a toast
                        },
                      ),
                    ],
                  ),
                ),
              ),
              
              // Shadow under Top Bar
              Positioned(
                top: 56,
                left: 0,
                right: 0,
                height: 1,
                child: Container(
                  color: Colors.grey.shade300,
                  height: 1,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
