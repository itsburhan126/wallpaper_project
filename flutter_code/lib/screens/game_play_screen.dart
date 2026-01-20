import 'dart:async';
import 'package:flutter/material.dart';
import 'package:webview_flutter/webview_flutter.dart';
import 'package:provider/provider.dart';
import 'package:glassmorphism/glassmorphism.dart';
import 'package:flutter_animate/flutter_animate.dart';
import '../models/game_model.dart';
import '../providers/app_provider.dart';
import '../services/mock_ad_service.dart';
import '../utils/constants.dart';

class GamePlayScreen extends StatefulWidget {
  final Game game;

  const GamePlayScreen({super.key, required this.game});

  @override
  State<GamePlayScreen> createState() => _GamePlayScreenState();
}

class _GamePlayScreenState extends State<GamePlayScreen> {
  late final WebViewController _controller;
  bool _isLoading = true;
  Timer? _timer;
  int _secondsPlayed = 0;
  bool _canClaimReward = false;

  @override
  void initState() {
    super.initState();
    _controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setBackgroundColor(const Color(0x00000000))
      ..setNavigationDelegate(
        NavigationDelegate(
          onPageStarted: (String url) {},
          onPageFinished: (String url) {
            setState(() {
              _isLoading = false;
            });
            _startTimer();
          },
          onWebResourceError: (WebResourceError error) {},
        ),
      )
      ..loadRequest(Uri.parse(widget.game.url));
  }

  void _startTimer() {
    _timer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (!mounted) return;
      setState(() {
        _secondsPlayed++;
        if (_secondsPlayed >= widget.game.playTimeSeconds && !_canClaimReward) {
          _canClaimReward = true;
        }
      });
    });
  }

  @override
  void dispose() {
    _timer?.cancel();
    super.dispose();
  }

  void _claimReward() {
    MockAdService.showRewardedAd(context, () {
      Provider.of<AppProvider>(context, listen: false).addCoins(20);
      _showCongratulationDialog();
      setState(() {
        _canClaimReward = false; // Reset or keep it? User asked for "coin btn show hobe"
        _secondsPlayed = 0; // Reset timer for next reward?
        // Usually rewards are once per session or periodic.
        // User said: "time end hoyar por coin btn show hobe... close korle coin credit hobe"
        // I'll reset it so they can play again for more coins.
      });
    });
  }

  void _showCongratulationDialog() {
    showDialog(
      context: context,
      builder: (context) => Dialog(
        backgroundColor: Colors.transparent,
        child: GlassmorphicContainer(
          width: 300,
          height: 250,
          borderRadius: 20,
          blur: 20,
          alignment: Alignment.center,
          border: 2,
          linearGradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [Colors.purple.withOpacity(0.2), Colors.blue.withOpacity(0.1)]),
          borderGradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [Colors.purple.withOpacity(0.5), Colors.blue.withOpacity(0.1)]),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.videogame_asset, color: Colors.purpleAccent, size: 60)
                  .animate()
                  .shake(duration: 500.ms),
              const SizedBox(height: 20),
              Text("Game Reward!", style: AppTextStyles.header),
              const SizedBox(height: 10),
              Text("You earned 20 Coins!", style: AppTextStyles.body),
              const SizedBox(height: 20),
              ElevatedButton(
                onPressed: () => Navigator.pop(context),
                child: const Text("Keep Playing"),
              )
            ],
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.game.title),
        backgroundColor: AppColors.background,
      ),
      body: Stack(
        children: [
          WebViewWidget(controller: _controller),
          if (_isLoading)
            const Center(child: CircularProgressIndicator()),
          
          // Reward Button
          if (_canClaimReward)
            Positioned(
              bottom: 30,
              right: 20,
              child: GestureDetector(
                onTap: _claimReward,
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
                  decoration: BoxDecoration(
                    color: Colors.green,
                    borderRadius: BorderRadius.circular(30),
                    boxShadow: [
                      BoxShadow(color: Colors.green.withOpacity(0.5), blurRadius: 10, spreadRadius: 2)
                    ],
                  ),
                  child: Row(
                    children: const [
                      Icon(Icons.card_giftcard, color: Colors.white),
                      SizedBox(width: 8),
                      Text("Claim Reward", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
                    ],
                  ),
                ).animate().scale(duration: 300.ms, curve: Curves.elasticOut),
              ),
            ),
        ],
      ),
    );
  }
}
