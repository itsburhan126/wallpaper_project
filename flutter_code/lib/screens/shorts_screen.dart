import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:video_player/video_player.dart';
import 'package:visibility_detector/visibility_detector.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:share_plus/share_plus.dart';
import 'package:timeago/timeago.dart' as timeago;
import '../models/short_model.dart';
import '../providers/shorts_provider.dart';
import '../providers/app_provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../services/google_ad_service.dart';
import '../providers/ad_provider.dart';
import '../services/ad_manager_service.dart';
import '../widgets/user_avatar.dart';
import '../dialog/reward_dialog.dart';
import '../widgets/toast/professional_toast.dart';
import '../dialog/ad_error_dialog.dart';

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
         GoogleAdService().loadRewardedAd(context);
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
      backgroundColor: Colors.black,
      body: Consumer<ShortsProvider>(
        builder: (context, provider, child) {
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
                  const Text(
                    "No shorts available yet",
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 18,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    "Check back later for new content!",
                    style: TextStyle(color: Colors.grey[500]),
                  ),
                  const SizedBox(height: 24),
                  ElevatedButton.icon(
                    onPressed: () {
                      provider.fetchShorts();
                    },
                    icon: const Icon(Icons.refresh),
                    label: const Text("Refresh"),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.indigo,
                      foregroundColor: Colors.white,
                    ),
                  )
                ],
              ),
            );
          }

          return PageView.builder(
            controller: _pageController,
            scrollDirection: Axis.vertical,
            itemCount: provider.shorts.length,
            itemBuilder: (context, index) {
              return ShortItem(
                short: provider.shorts[index],
                rewardTimer: provider.rewardTimer,
                rewardAmount: provider.rewardAmount,
              );
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
          color: Colors.black.withOpacity(0.3),
          boxShadow: isClaimable
              ? [
                  BoxShadow(
                    color: Colors.amber.withOpacity(0.5),
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
                      "CLAIM",
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
              .shimmer(duration: 2000.ms, color: Colors.white.withOpacity(0.4))
            else
              Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(Icons.card_giftcard_rounded, color: Colors.white, size: 28)
                      .animate(onPlay: (c) => c.repeat(reverse: true))
                      .rotate(begin: -0.05, end: 0.05, duration: 1000.ms),
                  const SizedBox(height: 2),
                  Text(
                    "${remainingTime}s",
                    style: GoogleFonts.poppins(
                      color: Colors.white,
                      fontWeight: FontWeight.bold,
                      fontSize: 12,
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
      ..color = Colors.white.withOpacity(0.15)
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

class _ShortItemState extends State<ShortItem> with SingleTickerProviderStateMixin {
  VideoPlayerController? _controller;
  bool _isPlaying = false;
  bool _showRewardButton = false;
  bool _rewardClaimed = false;
  int _elapsedTime = 0;
  
  // Timer logic
  bool _timerStarted = false;

  @override
  void initState() {
    super.initState();
    _initializePlayer();
  }

  void _initializePlayer() {
    _controller = VideoPlayerController.networkUrl(Uri.parse(widget.short.videoUrl))
      ..initialize().then((_) {
        if (mounted) {
          setState(() {
            _controller!.setLooping(true);
          });
          if (_isPlaying) {
            _controller!.play();
          }
        }
      }).catchError((error) {
        debugPrint("Video Error: $error");
        if (mounted) setState(() {}); // Trigger rebuild to show error UI
      });
      
    _controller!.addListener(_videoListener);
  }

  void _videoListener() {
    if (_controller != null && _controller!.value.hasError) {
      if (mounted) setState(() {});
    }
  }

  // Called when this page becomes visible
  void _onVisibilityChanged(VisibilityInfo info) {
    if (info.visibleFraction > 0.8) {
      _controller?.play();
      _isPlaying = true;
      _startRewardTimer();
    } else {
      _controller?.pause();
      _isPlaying = false;
    }
  }

  void _startRewardTimer() async {
    if (_timerStarted || _rewardClaimed) return;
    _timerStarted = true;

    // Simple countdown simulation based on actual video play time
    while (mounted && !_rewardClaimed) {
      await Future.delayed(const Duration(seconds: 1));
      if (_isPlaying) {
        setState(() {
          _elapsedTime++;
        });
        
        if (_elapsedTime >= widget.rewardTimer) {
           setState(() {
             _showRewardButton = true;
           });
           // Don't break loop if we want to keep "ready" state, 
           // but technically we can stop counting up if we capped it.
           // Let's cap it at rewardTimer for progress bar purposes.
           if (_elapsedTime > widget.rewardTimer) {
             _elapsedTime = widget.rewardTimer;
           }
        }
      }
    }
  }
  
  void _shareShort() {
    Share.share(
      'Check out this awesome short! ${widget.short.title}\n\n${widget.short.videoUrl}',
      subject: 'Watch ${widget.short.title} on Wallpaper App',
    );
  }

  void _showCommentsSheet() {
    _controller?.pause();
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
      if (!_rewardClaimed && !_isPlaying) _controller?.play();
    });
  }

  void _showRewardSheet() {
    _controller?.pause();
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
      if (!_rewardClaimed && !_isPlaying) _controller?.play();
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

    final provider = Provider.of<ShortsProvider>(context, listen: false);
    
    // Construct fallback list from provider, filtering out empty strings
    // If provider values are empty, default to ['admob'] for testing/demo
    List<String> fallbacks = [
      provider.adFallback1, 
      provider.adFallback2, 
      provider.adFallback3
    ].where((s) => s.isNotEmpty).toList();
    
    if (fallbacks.isEmpty) {
      fallbacks = ['admob']; // Default fallback
    }
    
    // Close Loading Dialog before showing ads
    if (showLoading && mounted) Navigator.pop(context);

    // Call AdManager (which now internally forces AdMob only as requested)
    bool success = await AdManager.showAdWithFallback(
      context, 
      fallbacks, 
      _onAdSuccess
    );

    if (!success) {
      ProfessionalToast.showError(context, message: "Ads not ready. Please try again later.");
    }
  }

  void _onAdSuccess() {
      // API Call to claim reward
      Provider.of<ShortsProvider>(context, listen: false).claimReward(widget.short.id).then((success) {
        if (success) {
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
      _controller?.play();
  }

  @override
  void dispose() {
    _controller?.removeListener(_videoListener);
    _controller?.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return VisibilityDetector(
      key: Key(widget.short.id),
      onVisibilityChanged: _onVisibilityChanged,
      child: Stack(
        fit: StackFit.expand,
        children: [
          // Video Layer
          GestureDetector(
            onTap: () {
              if (_controller != null && _controller!.value.isInitialized) {
                if (_controller!.value.isPlaying) {
                  _controller!.pause();
                  _isPlaying = false;
                } else {
                  _controller!.play();
                  _isPlaying = true;
                }
                setState(() {});
              }
            },
            child: Container(
              color: Colors.black,
              child: _controller != null && _controller!.value.hasError
                  ? Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          const Icon(Icons.error_outline, color: Colors.red, size: 40),
                          const SizedBox(height: 8),
                          const Text(
                            "Failed to load video",
                            style: TextStyle(color: Colors.white),
                          ),
                          if (_controller!.value.errorDescription != null)
                            Padding(
                              padding: const EdgeInsets.all(8.0),
                              child: Text(
                                _controller!.value.errorDescription!,
                                style: const TextStyle(color: Colors.grey, fontSize: 10),
                                textAlign: TextAlign.center,
                              ),
                            ),
                        ],
                      ),
                    )
                  : (_controller != null && _controller!.value.isInitialized
                      ? SizedBox.expand(
                          child: FittedBox(
                            fit: BoxFit.cover,
                            child: SizedBox(
                              width: _controller!.value.size.width,
                              height: _controller!.value.size.height,
                              child: VideoPlayer(_controller!),
                            ),
                          ),
                        )
                      : const Center(child: CircularProgressIndicator())),
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
                _buildActionBtn(Icons.share, "Share", _shareShort),
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
              child: ProfessionalRewardButton(
                progress: (_elapsedTime / widget.rewardTimer).clamp(0.0, 1.0),
                isClaimable: _showRewardButton,
                remainingTime: (widget.rewardTimer - _elapsedTime).clamp(0, widget.rewardTimer),
                onTap: _showRewardButton ? _showRewardSheet : null,
              ),
            ),
        ],
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
              color: Colors.black.withOpacity(0.4),
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
                    color: Colors.black.withOpacity(0.05),
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
                        maxLines: 3,
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  _isPosting
                      ? const SizedBox(
                          width: 40,
                          height: 40,
                          child: Padding(
                            padding: EdgeInsets.all(10),
                            child: CircularProgressIndicator(strokeWidth: 2.5),
                          ),
                        )
                      : Container(
                          decoration: const BoxDecoration(
                            color: Colors.indigo,
                            shape: BoxShape.circle,
                          ),
                          child: IconButton(
                            onPressed: _postComment,
                            icon: const Icon(Icons.send_rounded, color: Colors.white, size: 20),
                            constraints: const BoxConstraints(minWidth: 44, minHeight: 44),
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
