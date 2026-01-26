import 'dart:async';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:visibility_detector/visibility_detector.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:share_plus/share_plus.dart';
import 'package:timeago/timeago.dart' as timeago;
import '../models/short_model.dart';
import '../providers/shorts_provider.dart';
import '../providers/app_provider.dart';
import '../providers/language_provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../providers/ad_provider.dart';
import '../services/ad_manager_service.dart';
import '../widgets/ads/shorts_native_ad.dart';
import '../widgets/user_avatar.dart';
import '../dialog/reward_dialog.dart';
import '../widgets/toast/professional_toast.dart';
import '../utils/app_theme.dart';

import 'package:url_launcher/url_launcher.dart';
import 'package:cached_video_player_plus/cached_video_player_plus.dart';
import 'package:video_player/video_player.dart';
import 'package:youtube_player_flutter/youtube_player_flutter.dart';
import 'package:cached_network_image/cached_network_image.dart';

class ShortsScreen extends StatefulWidget {
  const ShortsScreen({super.key});

  @override
  State<ShortsScreen> createState() => _ShortsScreenState();
}

class _ShortsScreenState extends State<ShortsScreen> {
  final PageController _pageController = PageController();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final provider = Provider.of<ShortsProvider>(context, listen: false);
      provider.fetchShorts();
      provider.fetchSettings();
      // Preload Ad Settings and Ads
      final adProvider = Provider.of<AdProvider>(context, listen: false);
      adProvider.fetchAdSettings().then((_) {
         if (mounted) {
           AdManager.preloadAds(context);
         }
      });
    });
  }

  @override
  void dispose() {
    _pageController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.darkBackgroundColor,
      body: Consumer2<ShortsProvider, LanguageProvider>(
        builder: (context, provider, languageProvider, child) {
          if (provider.isLoading && provider.shorts.isEmpty) {
            return const Center(child: CircularProgressIndicator());
          }

          if (provider.shorts.isEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.video_library_outlined, size: 80, color: Colors.grey[700]),
                  const SizedBox(height: 16),
                  Text(
                    languageProvider.getText('no_shorts'),
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 18,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    languageProvider.getText('check_back'),
                    style: TextStyle(color: Colors.grey[500]),
                  ),
                  const SizedBox(height: 24),
                  ElevatedButton.icon(
                    onPressed: () {
                      provider.fetchShorts();
                    },
                    icon: const Icon(Icons.refresh),
                    label: Text(languageProvider.getText('refresh')),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.indigo,
                      foregroundColor: Colors.white,
                    ),
                  )
                ],
              ),
            );
          }

          // Ad Logic
          final adProvider = Provider.of<AdProvider>(context);
          final String effectiveNetwork = adProvider.getNativeNetworkForScreen('shorts');
          final int adInterval = adProvider.shortsAdInterval;
          final bool showAds = effectiveNetwork != 'none' && adInterval > 0;

          int itemCount = provider.shorts.length;
          if (showAds) {
            itemCount += provider.shorts.length ~/ adInterval;
          }

          return PageView.builder(
            controller: _pageController,
            scrollDirection: Axis.vertical,
            itemCount: itemCount,
            allowImplicitScrolling: true, // Smoother scrolling
            physics: const TikTokScrollPhysics(parent: ClampingScrollPhysics()), // Custom Snappy Physics
            itemBuilder: (context, index) {
              if (showAds) {
                // Check if this is an ad slot
                if ((index + 1) % (adInterval + 1) == 0) {
                  return ShortsNativeAd(
                    onAdFailed: () {
                      // If ad fails, move to next page if possible
                      if (_pageController.hasClients) {
                         // Small delay to ensure frame is built
                         Future.delayed(const Duration(milliseconds: 100), () {
                            if (_pageController.hasClients && _pageController.page != null) {
                               int currentPage = _pageController.page!.round();
                               if (currentPage == index) {
                                  _pageController.nextPage(
                                    duration: const Duration(milliseconds: 300),
                                    curve: Curves.easeOut,
                                  );
                               }
                            }
                         });
                      }
                    },
                  );
                }

                // Calculate original index
                int adsBefore = (index + 1) ~/ (adInterval + 1);
                int shortIndex = index - adsBefore;

                if (shortIndex < provider.shorts.length) {
                  return ShortItem(
                    short: provider.shorts[shortIndex],
                    rewardTimer: provider.rewardTimer,
                    rewardAmount: provider.rewardAmount,
                  );
                }
              } else {
                return ShortItem(
                  short: provider.shorts[index],
                  rewardTimer: provider.rewardTimer,
                  rewardAmount: provider.rewardAmount,
                );
              }
              return const SizedBox();
            },
          );
        },
      ),
    );
  }
}

class ProfessionalRewardButton extends StatelessWidget {
  final double progress;
  final bool isClaimable;
  final VoidCallback? onTap;
  final int remainingTime;

  const ProfessionalRewardButton({
    super.key,
    required this.progress,
    required this.isClaimable,
    this.onTap,
    required this.remainingTime,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        width: 90,
        height: 90,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          color: Colors.black.withValues(alpha: 0.3),
          boxShadow: isClaimable
              ? [
                  BoxShadow(
                    color: Colors.amber.withValues(alpha: 0.5),
                    blurRadius: 20,
                    spreadRadius: 2,
                  )
                ]
              : [],
        ),
        child: Stack(
          alignment: Alignment.center,
          children: [
            // Custom Progress Painter
            CustomPaint(
              size: const Size(90, 90),
              painter: RewardProgressPainter(
                progress: progress,
                isClaimable: isClaimable,
              ),
            ),
            
            // Content
            if (isClaimable)
              Container(
                width: 76,
                height: 76,
                decoration: const BoxDecoration(
                  shape: BoxShape.circle,
                  gradient: LinearGradient(
                    colors: [Colors.amber, Colors.orange, Colors.deepOrange],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                ),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Icon(Icons.stars_rounded, color: Colors.white, size: 32),
                    Text(
                      Provider.of<LanguageProvider>(context).getText('claim'),
                      style: GoogleFonts.poppins(
                        color: Colors.white,
                        fontWeight: FontWeight.w900,
                        fontSize: 13,
                        letterSpacing: 1,
                        height: 1.0,
                      ),
                    ),
                  ],
                ),
              )
              .animate(onPlay: (c) => c.repeat(reverse: true))
              .scale(begin: const Offset(0.95, 0.95), end: const Offset(1.05, 1.05), duration: 800.ms)
              .shimmer(duration: 2000.ms, color: Colors.white.withValues(alpha: 0.4))
            else
              Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(Icons.card_giftcard_rounded, color: Colors.white, size: 28)
                      .animate(onPlay: (c) => c.repeat(reverse: true))
                      .rotate(begin: -0.05, end: 0.05, duration: 1000.ms),
                  const SizedBox(height: 2),
                  Text(
                    "$remainingTime",
                    style: GoogleFonts.poppins(
                      color: Colors.white,
                      fontWeight: FontWeight.bold,
                      fontSize: 16,
                    ),
                  ),
                ],
              ),
          ],
        ),
      ),
    );
  }
}

class RewardProgressPainter extends CustomPainter {
  final double progress;
  final bool isClaimable;

  RewardProgressPainter({required this.progress, required this.isClaimable});

  @override
  void paint(Canvas canvas, Size size) {
    final center = Offset(size.width / 2, size.height / 2);
    final radius = (size.width - 8) / 2;

    // Background Circle
    final bgPaint = Paint()
      ..color = Colors.white.withValues(alpha: 0.15)
      ..style = PaintingStyle.stroke
      ..strokeWidth = 8;
    
    canvas.drawCircle(center, radius, bgPaint);

    // Progress Arc
    if (progress > 0) {
      final progressPaint = Paint()
        ..style = PaintingStyle.stroke
        ..strokeWidth = 8
        ..strokeCap = StrokeCap.round;
      
      if (isClaimable) {
        progressPaint.shader = const LinearGradient(
          colors: [Colors.amber, Colors.orangeAccent],
        ).createShader(Rect.fromCircle(center: center, radius: radius));
        
        // Add glow
        progressPaint.maskFilter = const MaskFilter.blur(BlurStyle.solid, 4);
      } else {
        progressPaint.shader = LinearGradient(
          colors: [Colors.amber.shade300, Colors.orange.shade300],
        ).createShader(Rect.fromCircle(center: center, radius: radius));
      }

      canvas.drawArc(
        Rect.fromCircle(center: center, radius: radius),
        -1.5708, // Start from top (-90 degrees)
        progress * 2 * 3.14159,
        false,
        progressPaint,
      );
    }
  }

  @override
  bool shouldRepaint(covariant RewardProgressPainter oldDelegate) {
    return oldDelegate.progress != progress || oldDelegate.isClaimable != isClaimable;
  }
}

class ShortItem extends StatefulWidget {
  final Short short;
  final int rewardTimer;
  final int rewardAmount;

  const ShortItem({
    super.key,
    required this.short,
    required this.rewardTimer,
    required this.rewardAmount,
  });

  @override
  State<ShortItem> createState() => _ShortItemState();
}

class _ShortItemState extends State<ShortItem> with SingleTickerProviderStateMixin, AutomaticKeepAliveClientMixin {
  CachedVideoPlayerPlus? _videoPlayer;
  YoutubePlayerController? _youtubeController;
  bool _isYoutube = false;
  bool _isPlaying = false;
  bool _showRewardButton = false;
  bool _rewardClaimed = false;
  bool _viewCounted = false;
  bool _isVideoReady = false; // To track if video is actually ready to render
  bool _isBuffering = false;
  bool _hasError = false;
  final ValueNotifier<int> _elapsedTimeNotifier = ValueNotifier<int>(0);
  
  // Timer logic
  bool _timerStarted = false;
  Timer? _initTimer;
  Timer? _rewardTimer;

  @override
  bool get wantKeepAlive => true;

  @override
  void initState() {
    super.initState();
    // Schedule initialization for the next frame to prevent scroll jank
    // This allows the PageView build phase to complete before heavy video setup starts
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (mounted) {
         _initializePlayer();
      }
    });
  }

  @override
  void dispose() {
    _initTimer?.cancel();
    _rewardTimer?.cancel();
    if (_videoPlayer != null && _videoPlayer!.isInitialized) {
      _videoPlayer!.controller.removeListener(_videoListener);
      _videoPlayer!.dispose();
    }
    _youtubeController?.dispose();
    _elapsedTimeNotifier.dispose();
    super.dispose();
  }

  void _initializePlayer() {
    if (!mounted) return;
    
    // Check if YouTube URL
    if (widget.short.videoUrl.contains('youtube.com') || widget.short.videoUrl.contains('youtu.be')) {
      _isYoutube = true;
      String? videoId = YoutubePlayer.convertUrlToId(widget.short.videoUrl);
      
      // Manual extraction for shorts if default fails
      if (videoId == null) {
        if (widget.short.videoUrl.contains("shorts/")) {
           try {
             final uri = Uri.parse(widget.short.videoUrl);
             if (uri.pathSegments.contains("shorts")) {
                videoId = uri.pathSegments.last;
             }
           } catch (e) {
             debugPrint("Error parsing Shorts URL: $e");
           }
        }
        // Try regex fallback for various youtube formats
        if (videoId == null) {
           RegExp regExp = RegExp(r"^(?:https?:\/\/)?(?:www\.|m\.)?youtu(?:be\.com|\.be)\/(?:shorts\/|watch\?v=|embed\/|v\/)?([\w-]{11})(?:\S+)?$");
           Match? match = regExp.firstMatch(widget.short.videoUrl);
           if (match != null && match.groupCount >= 1) {
             videoId = match.group(1);
           }
        }
      }

      if (videoId != null) {
        debugPrint("Initializing YouTube Player for ID: $videoId");
        _youtubeController = YoutubePlayerController(
          initialVideoId: videoId,
          flags: const YoutubePlayerFlags(
            autoPlay: false, // Must be false for smooth scrolling
            loop: true,
            mute: false,
            hideControls: true,
            forceHD: false,
            enableCaption: false,
            useHybridComposition: false, // Disable to reduce overhead
          ),
        )..addListener(_youtubeListener);
        
        if (mounted) {
           setState(() {});
           // If we are already visible (race condition with Timer), play now
           if (_isPlaying) {
             _youtubeController?.play();
           }
        }
      }
      return;
    }

    // Initialize CachedVideoPlayerPlus
    try {
      debugPrint("Initializing CachedVideoPlayerPlus for: ${widget.short.videoUrl}");
      _videoPlayer = CachedVideoPlayerPlus.networkUrl(
        Uri.parse(widget.short.videoUrl),
        videoPlayerOptions: VideoPlayerOptions(mixWithOthers: true),
        invalidateCacheIfOlderThan: const Duration(days: 90),
      );
      
      _videoPlayer!.initialize().then((_) {
        debugPrint("Video Initialized Successfully: ${widget.short.videoUrl}");
        if (mounted) {
          _videoPlayer!.controller.addListener(_videoListener);
          setState(() {
            _videoPlayer!.controller.setLooping(true);
            _videoPlayer!.controller.setVolume(1.0);
            _isVideoReady = true; 
            _isBuffering = false;
          });
          // Play only if already determined to be playing (via visibility)
          if (_isPlaying) {
             debugPrint("Starting Playback: ${widget.short.videoUrl}");
            _videoPlayer!.controller.play();
          }
        }
      }).catchError((error) {
        debugPrint("Video Initialization Error for ${widget.short.videoUrl}: $error");
        if (mounted) setState(() {});
      });
      
    } catch (e) {
       debugPrint("Error creating video player instance: $e");
    }
  }
  
  void _youtubeListener() {
    if (_youtubeController != null) {
      // Buffering Check
      final bool isBuffering = _youtubeController!.value.playerState == PlayerState.buffering;
      if (isBuffering != _isBuffering) {
         if (mounted) setState(() => _isBuffering = isBuffering);
      }

      if (_youtubeController!.value.hasError) {
        debugPrint("YouTube Error: ${_youtubeController!.value.errorCode}");
        if (mounted && !_hasError) {
           setState(() => _hasError = true);
        }
      }
      
      if (_youtubeController!.value.isReady && !_isVideoReady) {
         if(mounted) {
           setState(() {
             _isVideoReady = true;
           });
         }
      }
    }
  }

  void _videoListener() {
    if (_videoPlayer != null && _videoPlayer!.isInitialized) {
       final bool isBuffering = _videoPlayer!.controller.value.isBuffering;
       if (isBuffering != _isBuffering) {
          if (mounted) setState(() => _isBuffering = isBuffering);
       }

       if (_videoPlayer!.controller.value.hasError) {
          debugPrint("Video Player Error: ${_videoPlayer!.controller.value.errorDescription}");
          if (mounted && !_hasError) {
             setState(() => _hasError = true);
          }
       }
       if (_videoPlayer!.controller.value.isPlaying && !_isVideoReady) {
          if(mounted) {
             setState(() {
                _isVideoReady = true;
             });
          }
       }
    }
  }

  String _getFriendlyErrorMessage(String? error) {
    if (error == null) return '';
    if (error.contains('NO_EXCEEDS_CAPABILITIES')) {
      return "This video resolution is too high for your device.";
    }
    if (error.contains('Source error') || error.contains('403') || error.contains('404')) {
      return "Video source not found or access denied.";
    }
    if (error.contains('Network') || error.contains('connection') || error.contains('SocketException')) {
      return "Please check your internet connection.";
    }
    return "An unexpected error occurred while playing video.";
  }

  // Called when this page becomes visible
  void _onVisibilityChanged(VisibilityInfo info) {
    // Lower threshold for better UX (TikTok style)
    if (info.visibleFraction > 0.6) {
      if (!_viewCounted) {
        _viewCounted = true;
        Provider.of<ShortsProvider>(context, listen: false).incrementView(widget.short.id);
      }

      if (_isYoutube) {
        if (_youtubeController != null && !_youtubeController!.value.isPlaying) {
             _youtubeController!.play();
        }
      } else {
        if (_videoPlayer != null && _videoPlayer!.isInitialized) {
          if (!_videoPlayer!.controller.value.isPlaying) {
             _videoPlayer!.controller.play();
          }
        }
      }
      _isPlaying = true;
      _startRewardTimer();
    } else if (info.visibleFraction < 0.2) {
      // Pause when mostly hidden
      if (_isYoutube) {
        _youtubeController?.pause();
      } else {
        if (_videoPlayer != null && _videoPlayer!.isInitialized) {
          _videoPlayer!.controller.pause();
        }
      }
      _isPlaying = false;
    }
  }

  void _startRewardTimer() {
    if (_timerStarted || _rewardClaimed) return;
    _timerStarted = true;

    _rewardTimer?.cancel();
    _rewardTimer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (!mounted) {
        timer.cancel();
        return;
      }
      
      if (_isPlaying && !_rewardClaimed) {
        int newTime = _elapsedTimeNotifier.value + 1;
        _elapsedTimeNotifier.value = newTime;
        
        if (newTime >= widget.rewardTimer) {
           if (!_showRewardButton) {
             setState(() {
               _showRewardButton = true;
             });
           }
           if (newTime > widget.rewardTimer) {
             _elapsedTimeNotifier.value = widget.rewardTimer;
             timer.cancel();
           }
        }
      }
    });
  }
  
  void _shareShort() {
    Share.share(
      'Check out this awesome short! ${widget.short.title}\n\n${widget.short.videoUrl}',
      subject: 'Watch ${widget.short.title} on Wallpaper App',
    );
  }

  void _showCommentsSheet() {
    if (_videoPlayer != null && _videoPlayer!.isInitialized) _videoPlayer!.controller.pause();
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => Padding(
        padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
        child: DraggableScrollableSheet(
          initialChildSize: 0.7,
          minChildSize: 0.5,
          maxChildSize: 0.95,
          builder: (_, scrollController) => _CommentsSheet(
            shortId: widget.short.id,
            scrollController: scrollController,
          ),
        ),
      ),
    ).then((_) {
      if (!_rewardClaimed && !_isPlaying && _videoPlayer != null && _videoPlayer!.isInitialized) {
        _videoPlayer!.controller.play();
      }
    });
  }

  void _showRewardSheet() {
    if (_videoPlayer != null && _videoPlayer!.isInitialized) _videoPlayer!.controller.pause();
    final appProvider = Provider.of<AppProvider>(context, listen: false);
    
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => RewardDialog(
        rewardAmount: widget.rewardAmount,
        currencySymbol: appProvider.currencySymbol,
        coinRate: appProvider.coinRate,
        onReceive: () async {
          // Delay for button loading effect
          await Future.delayed(const Duration(milliseconds: 1000));
          
          if (context.mounted) {
            Navigator.pop(context);
            _processAdFallback(showLoading: false);
          }
        },
        onClose: () => Navigator.pop(context),
      ),
    ).then((_) {
      if (!_rewardClaimed && !_isPlaying && _videoPlayer != null && _videoPlayer!.isInitialized) {
         _videoPlayer!.controller.play();
      }
    });
  }
  
  // Ad Fallback Logic
  void _processAdFallback({bool showLoading = true}) async {
    // Show Loading
    if (showLoading) {
      showDialog(
        context: context,
        barrierDismissible: false,
        builder: (_) => const Center(child: CircularProgressIndicator()),
      );
    }

    final adProvider = Provider.of<AdProvider>(context, listen: false);
    
    // Use global priorities
    List<String> fallbacks = adProvider.adPriorities.isNotEmpty 
        ? adProvider.adPriorities 
        : ['admob', 'facebook', 'unity'];
    
    // Close Loading Dialog before showing ads
    if (showLoading && mounted) Navigator.pop(context);

    // Call AdManager
    bool success = await AdManager.showAdWithFallback(
      context, 
      fallbacks, 
      _onAdSuccess
    );

    if (!success) {
      if (mounted) {
        ProfessionalToast.showError(context, message: "Ads not available");
      }
    }
  }

  void _onAdSuccess() {
      // API Call to claim reward
      if (!mounted) return;
      Provider.of<ShortsProvider>(context, listen: false).claimReward(widget.short.id).then((success) {
        if (success && mounted) {
           Provider.of<AppProvider>(context, listen: false).addCoins(widget.rewardAmount);
            if (mounted) {
              setState(() {
                _rewardClaimed = true;
                _showRewardButton = false;
              });
              ProfessionalToast.show(context, coinAmount: widget.rewardAmount);
            }
        }
      });
      if (_videoPlayer != null && _videoPlayer!.isInitialized) {
        _videoPlayer!.controller.play();
      }
  }

  @override
  void didUpdateWidget(ShortItem oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (oldWidget.short.videoUrl != widget.short.videoUrl) {
       _initTimer?.cancel();
       if (_videoPlayer != null && _videoPlayer!.isInitialized) {
         _videoPlayer!.controller.removeListener(_videoListener);
         _videoPlayer!.dispose();
       }
       _youtubeController?.dispose();
       _videoPlayer = null;
       _youtubeController = null;
       _isYoutube = false;
       _isPlaying = false;
       _isVideoReady = false;
       _hasError = false;
       _timerStarted = false;
       _elapsedTimeNotifier.value = 0;
       _rewardClaimed = false;
       _showRewardButton = false;
       _viewCounted = false;
       // Immediate re-initialization
       _initializePlayer();
    }
  }



  @override
  Widget build(BuildContext context) {
    super.build(context);
    return RepaintBoundary(
      child: VisibilityDetector(
        key: Key('short_${widget.short.id}'),
        onVisibilityChanged: _onVisibilityChanged,
        child: Stack(
          fit: StackFit.expand,
          children: [
          // Video Layer
          GestureDetector(
            onTap: () {
              if (_isYoutube && _youtubeController != null) {
                 if (_youtubeController!.value.isPlaying) {
                   _youtubeController!.pause();
                   _isPlaying = false;
                 } else {
                   _youtubeController!.play();
                   _isPlaying = true;
                 }
                 setState(() {});
                 return;
              }

              if (_videoPlayer != null && _videoPlayer!.isInitialized) {
                if (_videoPlayer!.controller.value.isPlaying) {
                  _videoPlayer!.controller.pause();
                  _isPlaying = false;
                } else {
                  _videoPlayer!.controller.play();
                  _isPlaying = true;
                }
                setState(() {});
              }
            },
            child: Container(
              color: Colors.black,
              child: Stack(
                fit: StackFit.expand,
                children: [
                   // 1. Video Layer
                   if (_hasError || (_videoPlayer != null && _videoPlayer!.isInitialized && _videoPlayer!.controller.value.hasError))
                      Center(
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            const Icon(Icons.error_outline_rounded, color: Colors.white54, size: 48),
                            const SizedBox(height: 16),
                            Text(
                              Provider.of<LanguageProvider>(context).getText('failed_load_video'),
                              style: GoogleFonts.poppins(
                                color: Colors.white,
                                fontSize: 16,
                                fontWeight: FontWeight.w600
                              ),
                            ),
                            const SizedBox(height: 8),
                            Padding(
                              padding: const EdgeInsets.symmetric(horizontal: 32.0),
                              child: Text(
                                _isYoutube 
                                   ? "Video unavailable or ID invalid." 
                                   : _getFriendlyErrorMessage(_videoPlayer?.controller.value.errorDescription),
                                style: GoogleFonts.poppins(color: Colors.white54, fontSize: 12),
                                textAlign: TextAlign.center,
                              ),
                            ),
                            const SizedBox(height: 24),
                            ElevatedButton.icon(
                              onPressed: () {
                                 setState(() {
                                    _hasError = false;
                                 });
                                 _initializePlayer();
                              },
                              icon: const Icon(Icons.refresh_rounded, size: 18),
                              label: Text(Provider.of<LanguageProvider>(context).getText('refresh')),
                               style: ElevatedButton.styleFrom(
                                 backgroundColor: Colors.white.withValues(alpha: 0.1),
                                 foregroundColor: Colors.white,
                                 elevation: 0,
                                 shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(30)),
                                 padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                               ),
                             ),
                             const SizedBox(height: 16),
                             TextButton.icon(
                               onPressed: () async {
                                 final uri = Uri.parse(widget.short.videoUrl);
                                 if (await canLaunchUrl(uri)) {
                                   await launchUrl(uri, mode: LaunchMode.externalApplication);
                                 }
                               },
                               icon: const Icon(Icons.open_in_new_rounded, size: 16),
                               label: Text(
                                 "Open in External Player",
                                 style: GoogleFonts.poppins(color: Colors.white70, fontSize: 13),
                               ),
                               style: TextButton.styleFrom(
                                 foregroundColor: Colors.white70,
                               ),
                             )
                          ],
                        ),
                      )
                   else if (_isYoutube && _youtubeController != null)
                      YoutubePlayer(
                        controller: _youtubeController!,
                        showVideoProgressIndicator: true,
                        progressIndicatorColor: Colors.amber,
                        progressColors: const ProgressBarColors(
                          playedColor: Colors.amber,
                          handleColor: Colors.amberAccent,
                        ),
                      )
                   else if (_videoPlayer != null && _videoPlayer!.isInitialized)
                        SizedBox.expand(
                          child: FittedBox(
                            fit: BoxFit.cover,
                            child: SizedBox(
                              width: _videoPlayer!.controller.value.size.width,
                              height: _videoPlayer!.controller.value.size.height,
                              child: VideoPlayer(_videoPlayer!.controller),
                            ),
                          ),
                        )
                   else 
                        const SizedBox(),

                   // 2. Thumbnail Layer (Overlay until ready)
                   if (widget.short.thumbnailUrl != null && !_isVideoReady)
                     CachedNetworkImage(
                       imageUrl: widget.short.thumbnailUrl!,
                       fit: BoxFit.cover,
                       memCacheHeight: MediaQuery.of(context).size.height.toInt(),
                       placeholder: (context, url) => Container(color: Colors.black),
                       errorWidget: (context, url, error) => Container(color: Colors.black),
                     ),

                   // 3. Loading Indicator
                   if ((widget.short.thumbnailUrl == null && !_isVideoReady && !_hasError) || _isBuffering)
                      const Center(
                        child: CircularProgressIndicator(
                          strokeWidth: 3,
                          color: Colors.white,
                        ),
                      ),
                ],
              ),
            ),
          ),

          // Right Side Actions
          Positioned(
            right: 10,
            bottom: 100,
            child: Column(
              children: [
                _buildActionBtn(
                  widget.short.isLiked ? Icons.favorite : Icons.favorite_border,
                  "${widget.short.likes}",
                  () {
                   Provider.of<ShortsProvider>(context, listen: false).likeShort(widget.short.id);
                  },
                  color: widget.short.isLiked ? Colors.red : Colors.white,
                  isLiked: widget.short.isLiked,
                ),
                const SizedBox(height: 20),
                _buildActionBtn(Icons.comment, "${widget.short.commentsCount}", _showCommentsSheet),
                const SizedBox(height: 20),
                _buildActionBtn(Icons.share, Provider.of<LanguageProvider>(context).getText('share'), _shareShort),
              ],
            ),
          ),

          // Bottom Info
          Positioned(
            left: 10,
            bottom: 20,
            right: 80,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  widget.short.title,
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ],
            ),
          ),

          // Reward Button (Progress & Claim)
          if (!_rewardClaimed)
            Positioned(
              top: 50,
              right: 16,
              child: ValueListenableBuilder<int>(
                valueListenable: _elapsedTimeNotifier,
                builder: (context, elapsedTime, child) {
                  return ProfessionalRewardButton(
                    progress: (elapsedTime / widget.rewardTimer).clamp(0.0, 1.0),
                    isClaimable: _showRewardButton,
                    remainingTime: (widget.rewardTimer - elapsedTime).clamp(0, widget.rewardTimer),
                    onTap: _showRewardButton ? _showRewardSheet : null,
                  );
                },
              ),
            ),
        ],
      ),
    ),
  );
}

  Widget _buildActionBtn(IconData icon, String label, VoidCallback onTap, {Color color = Colors.white, bool isLiked = false}) {
    return GestureDetector(
      onTap: onTap,
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: Colors.black.withValues(alpha: 0.4),
              shape: BoxShape.circle,
            ),
            child: Icon(icon, color: color, size: 30)
                .animate(target: isLiked ? 1 : 0)
                .scale(duration: 200.ms, curve: Curves.easeInOut, begin: const Offset(1, 1), end: const Offset(1.2, 1.2))
                .then()
                .scale(duration: 200.ms, curve: Curves.easeInOut, begin: const Offset(1.2, 1.2), end: const Offset(1, 1)),
          ),
          const SizedBox(height: 5),
          Text(label, style: const TextStyle(color: Colors.white)),
        ],
      ),
    );
  }
}

class _CommentsSheet extends StatefulWidget {
  final String shortId;
  final ScrollController scrollController;

  const _CommentsSheet({required this.shortId, required this.scrollController});

  @override
  State<_CommentsSheet> createState() => _CommentsSheetState();
}

class TikTokScrollPhysics extends PageScrollPhysics {
  const TikTokScrollPhysics({super.parent});

  @override
  TikTokScrollPhysics applyTo(ScrollPhysics? ancestor) {
    return TikTokScrollPhysics(parent: buildParent(ancestor));
  }

  @override
  double get minFlingVelocity => 1.0; 

  @override
  double get minFlingDistance => 1.0; 

  @override
  SpringDescription get spring => const SpringDescription(
        mass: 1.0, 
        stiffness: 400.0, 
        damping: 40.0,
      );
}
class _CommentsSheetState extends State<_CommentsSheet> {
  final TextEditingController _commentController = TextEditingController();
  List<Comment> _comments = [];
  bool _isLoading = true;
  bool _isPosting = false;

  @override
  void initState() {
    super.initState();
    _fetchComments();
  }

  Future<void> _fetchComments() async {
    final comments = await Provider.of<ShortsProvider>(context, listen: false)
        .fetchComments(widget.shortId);
    if (mounted) {
      setState(() {
        _comments = comments;
        _isLoading = false;
      });
    }
  }

  Future<void> _postComment() async {
    if (_commentController.text.trim().isEmpty) return;

    setState(() => _isPosting = true);
    
    final newComment = await Provider.of<ShortsProvider>(context, listen: false)
        .postComment(widget.shortId, _commentController.text.trim());

    if (mounted) {
      setState(() => _isPosting = false);
      if (newComment != null) {
        _commentController.clear();
        setState(() {
          _comments.insert(0, newComment);
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      child: Column(
        children: [
            Container(
              width: 40,
              height: 4,
              margin: const EdgeInsets.symmetric(vertical: 12),
              decoration: BoxDecoration(
                color: Colors.grey[300],
                borderRadius: BorderRadius.circular(2),
              ),
            ),
            Padding(
              padding: const EdgeInsets.only(bottom: 16),
              child: Text(
                "${_comments.length} Comments",
                style: const TextStyle(
                  fontWeight: FontWeight.bold, 
                  fontSize: 18,
                  color: Colors.black87,
                ),
              ),
            ),
            Expanded(
              child: _isLoading
                  ? const Center(child: CircularProgressIndicator())
                  : _comments.isEmpty
                      ? Center(
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(Icons.chat_bubble_outline, size: 60, color: Colors.grey[400]),
                              const SizedBox(height: 12),
                              Text(
                                "No comments yet",
                                style: TextStyle(color: Colors.grey[700], fontSize: 16, fontWeight: FontWeight.w500),
                              ),
                              const SizedBox(height: 4),
                              Text(
                                "Be the first to share your thoughts!",
                                style: TextStyle(color: Colors.grey[500], fontSize: 14),
                              ),
                            ],
                          ),
                        )
                      : ListView.builder(
                          controller: widget.scrollController,
                          itemCount: _comments.length,
                          padding: const EdgeInsets.symmetric(horizontal: 20),
                          keyboardDismissBehavior: ScrollViewKeyboardDismissBehavior.onDrag,
                          itemBuilder: (context, index) {
                            final comment = _comments[index];
                            return Padding(
                              padding: const EdgeInsets.only(bottom: 20),
                              child: Row(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  UserAvatar(
                                    imageUrl: comment.userImage,
                                    userName: comment.userName,
                                    radius: 20,
                                    backgroundColor: Colors.indigo.shade50,
                                  ),
                                  const SizedBox(width: 14),
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Row(
                                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                          children: [
                                            Text(
                                              comment.userName,
                                              style: const TextStyle(
                                                fontWeight: FontWeight.bold,
                                                fontSize: 14,
                                                color: Colors.black87,
                                              ),
                                            ),
                                            Text(
                                              timeago.format(DateTime.tryParse(comment.createdAt) ?? DateTime.now()),
                                              style: TextStyle(
                                                color: Colors.grey[600],
                                                fontSize: 11,
                                              ),
                                            ),
                                          ],
                                        ),
                                        const SizedBox(height: 6),
                                        Container(
                                          padding: const EdgeInsets.all(12),
                                          decoration: BoxDecoration(
                                            color: Colors.grey[50],
                                            borderRadius: BorderRadius.circular(12),
                                            border: Border.all(color: Colors.grey.shade200),
                                          ),
                                          child: Text(
                                            comment.body,
                                            style: const TextStyle(
                                              fontSize: 14, 
                                              height: 1.4,
                                              color: Colors.black87,
                                            ),
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                ],
                              ),
                            );
                          },
                        ),
            ),
            Container(
              padding: EdgeInsets.fromLTRB(16, 12, 16, 16 + MediaQuery.of(context).padding.bottom),
              decoration: BoxDecoration(
                color: Colors.white,
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: 0.05),
                    blurRadius: 10,
                    offset: const Offset(0, -5),
                  ),
                ],
              ),
              child: Row(
                children: [
                  Expanded(
                    child: Container(
                      decoration: BoxDecoration(
                        color: Colors.grey[100],
                        borderRadius: BorderRadius.circular(24),
                        border: Border.all(color: Colors.grey.shade300),
                      ),
                      child: TextField(
                        controller: _commentController,
                        style: const TextStyle(color: Colors.black87),
                        decoration: InputDecoration(
                          hintText: "Add a comment...",
                          hintStyle: TextStyle(color: Colors.grey[500]),
                          border: InputBorder.none,
                          contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                          isDense: true,
                        ),
                        minLines: 1,
                        maxLines: 4,
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  GestureDetector(
                    onTap: _isPosting ? null : _postComment,
                    child: Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: _isPosting ? Colors.grey : Colors.indigo,
                        shape: BoxShape.circle,
                        boxShadow: [
                          BoxShadow(
                            color: (_isPosting ? Colors.grey : Colors.indigo).withValues(alpha: 0.3),
                            blurRadius: 8,
                            offset: const Offset(0, 4),
                          ),
                        ],
                      ),
                      child: _isPosting
                          ? const SizedBox(
                              width: 24,
                              height: 24,
                              child: CircularProgressIndicator(
                                strokeWidth: 2,
                                valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                              ),
                            )
                          : const Icon(Icons.send_rounded, color: Colors.white, size: 24),
                    ),
                  ),
                ],
              ),
            ),
        ],
      ),
    );
  }
}
