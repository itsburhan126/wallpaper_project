import 'package:cached_network_image/cached_network_image.dart';
import 'package:wallpaper_app/widgets/lucky_wheel_dialog.dart';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:url_launcher/url_launcher.dart';
import '../providers/app_provider.dart';
import '../providers/ad_provider.dart';
import '../services/ad_manager_service.dart';
import '../utils/constants.dart';
import '../dialog/reward_dialog.dart';
import '../dialog/game_reward_dialog.dart';
import '../dialog/game_warning_dialog.dart';
import '../dialog/game_limit_dialog.dart';
import '../dialog/ad_limit_dialog.dart';
import '../dialog/limit_reached_sheet.dart';
import '../widgets/coin_animation_overlay.dart';
import '../services/google_ad_service.dart';
import '../widgets/toast/professional_toast.dart';
import 'game_webview_screen.dart';
import 'all_games_screen.dart';

import '../widgets/animated_coin_balance.dart';
import 'referral_screen.dart';

class TaskScreen extends StatefulWidget {
  const TaskScreen({super.key});

  @override
  State<TaskScreen> createState() => _TaskScreenState();
}

class _TaskScreenState extends State<TaskScreen> {
  final GlobalKey _coinIconKey = GlobalKey();
  bool _isWatchAdLoading = false;

  @override
  void initState() {
    super.initState();
    // Fetch user coin balance and games
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final appProvider = Provider.of<AppProvider>(context, listen: false);
      appProvider.fetchUserBalance();
      appProvider.fetchGames();
      
      // Preload Ads for smoother experience
      GoogleAdService().loadRewardedAd(context);
      GoogleAdService().loadInterstitialAd(context);
    });
  }

  Future<void> _showGameRewardDialog(BuildContext context, int rewardAmount, String gameId) async {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (ctx) => GameRewardDialog(
        baseReward: rewardAmount,
        onClaim: () async {
          if (context.mounted) Navigator.pop(ctx);
          await Future.delayed(const Duration(milliseconds: 300));
          if (context.mounted) await _claimGameReward(context, rewardAmount, 1, gameId);
        },
        onClaim2x: () async {
          // Don't pop immediately, let the button show loading state
          
          final adProvider = Provider.of<AdProvider>(context, listen: false);
          bool adShown = await AdManager.showAdWithFallback(
            context, 
            adProvider.adPriorities, 
            () async {
               if (ctx.mounted) Navigator.pop(ctx);
               await Future.delayed(const Duration(milliseconds: 500));
               if (context.mounted) await _claimGameReward(context, rewardAmount, 2, gameId);
            }
          );

          if (!adShown && context.mounted) {
             ProfessionalToast.showError(context, message: "Ads not available");
          }
        },
        onClose: () {
          Navigator.pop(ctx);
        },
      ),
    );
  }

  Future<void> _claimGameReward(BuildContext context, int baseReward, int multiplier, String gameId) async {
    final provider = Provider.of<AppProvider>(context, listen: false);
    final totalReward = baseReward * multiplier;
    
    // Show animation first, then add coins
    if (context.mounted) {
      CoinAnimationOverlay.show(
        context, 
        _coinIconKey, 
        coinCount: 10,
        onComplete: () async {
          await provider.addCoins(totalReward, source: 'game_reward', gameId: gameId);
          print("🎮 [Game Play] Coins Credited: $totalReward | Games Played Today: ${provider.gamesPlayedToday} | Daily Limit: ${provider.gameDailyLimit}");
          if (context.mounted) {
            ProfessionalToast.showSuccess(context, message: "You earned $totalReward coins!");
          }
        },
      );
    }
  }



  @override
  Widget build(BuildContext context) {
    // DEBUG LOGS
    final debugProvider = Provider.of<AppProvider>(context);
    print("------- TASK SCREEN DEBUG -------");
    print("Coins: ${debugProvider.coins}");
    print("Name: ${debugProvider.userName}");
    print("Avatar URL: ${debugProvider.userAvatar}");
    print("---------------------------------");

    return Scaffold(
      backgroundColor: const Color(0xFF120C24), // Deep Purple/Black background
      body: RefreshIndicator(
        backgroundColor: const Color(0xFF2A1B4E),
        color: Colors.white,
        onRefresh: () async {
          final provider = Provider.of<AppProvider>(context, listen: false);
          await Future.wait([
            provider.fetchUserBalance(),
            provider.fetchGames(),
          ]);
        },
        child: CustomScrollView(
          physics: const BouncingScrollPhysics(parent: AlwaysScrollableScrollPhysics()),
          slivers: [
            // Custom App Bar with User Profile
          SliverAppBar(
            backgroundColor: const Color(0xFF120C24),
            expandedHeight: 65.0,
            floating: true,
            pinned: true,
            elevation: 0,
            flexibleSpace: FlexibleSpaceBar(
              background: Container(
                decoration: const BoxDecoration(
                  gradient: LinearGradient(
                    colors: [Color(0xFF2E1C59), Color(0xFF120C24)],
                    begin: Alignment.topCenter,
                    end: Alignment.bottomCenter,
                  ),
                ),
              ),
              titlePadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
              title: Row(
                children: [
                  // 1. Professional Avatar with Gradient Border (Extra Small)
                  Consumer<AppProvider>(
                    builder: (context, appProv, _) {
                      return Container(
                        width: 32,
                        height: 32,
                        padding: const EdgeInsets.all(1.5),
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          gradient: const LinearGradient(
                            colors: [Color(0xFFFFD700), Color(0xFFFFA000)], // Gold Gradient
                            begin: Alignment.topLeft,
                            end: Alignment.bottomRight,
                          ),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.amber.withOpacity(0.4),
                              blurRadius: 6,
                              spreadRadius: 1,
                            )
                          ],
                        ),
                        child: Container(
                          decoration: const BoxDecoration(
                            shape: BoxShape.circle,
                            color: Color(0xFF2A1B4E),
                          ),
                          child: appProv.userAvatar.isNotEmpty
                              ? ClipOval(
                                  child: CachedNetworkImage(
                                    imageUrl: appProv.userAvatar,
                                    fit: BoxFit.cover,
                                    width: 32,
                                    height: 32,
                                    placeholder: (context, url) => const Icon(Icons.person, color: Colors.white, size: 20),
                                    errorWidget: (context, url, error) => const Icon(Icons.person, color: Colors.white, size: 20),
                                  ),
                                )
                              : const Icon(Icons.person, color: Colors.white, size: 20),
                        ),
                      );
                    },
                  ),
                  const SizedBox(width: 8),

                  // 2. User Info (Very Compact)
                  Expanded(
                    child: Consumer<AppProvider>(
                      builder: (context, provider, child) {
                        if (provider.isUserLoading && provider.userName == "Guest User") {
                           return Column(
                             mainAxisSize: MainAxisSize.min,
                             crossAxisAlignment: CrossAxisAlignment.start,
                             children: [
                               Container(
                                 width: 80, 
                                 height: 14, 
                                 decoration: BoxDecoration(
                                   color: Colors.white.withOpacity(0.1),
                                   borderRadius: BorderRadius.circular(4)
                                 )
                               ).animate(onPlay: (c) => c.repeat()).shimmer(duration: 1200.ms, color: Colors.white.withOpacity(0.2)),
                               const SizedBox(height: 3),
                               Container(
                                 width: 40, 
                                 height: 10, 
                                 decoration: BoxDecoration(
                                   color: Colors.white.withOpacity(0.1),
                                   borderRadius: BorderRadius.circular(8)
                                 )
                               ).animate(onPlay: (c) => c.repeat()).shimmer(duration: 1200.ms, color: Colors.white.withOpacity(0.2)),
                             ],
                           );
                        }

                        return Column(
                          mainAxisSize: MainAxisSize.min,
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              provider.userName.isNotEmpty ? provider.userName : "Guest Player",
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                              style: GoogleFonts.poppins(
                                fontSize: 12,
                                fontWeight: FontWeight.bold,
                                color: Colors.white,
                                shadows: [
                                  const Shadow(color: Colors.black54, offset: Offset(0, 1), blurRadius: 2)
                                ],
                              ),
                            ),
                            const SizedBox(height: 1),
                            // Level Badge (Extra Small)
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 1),
                              decoration: BoxDecoration(
                                gradient: const LinearGradient(
                                  colors: [Color(0xFFFFA000), Color(0xFFFF6F00)],
                                  begin: Alignment.centerLeft,
                                  end: Alignment.centerRight,
                                ),
                                borderRadius: BorderRadius.circular(10),
                                boxShadow: [
                                  BoxShadow(
                                    color: Colors.orange.withOpacity(0.4),
                                    blurRadius: 1,
                                    offset: const Offset(0, 1),
                                  )
                                ],
                              ),
                              child: Row(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  const Icon(Icons.stars_rounded, color: Colors.white, size: 8),
                                  const SizedBox(width: 2),
                                  Text(
                                    "Lvl ${provider.userLevel}",
                                    style: GoogleFonts.poppins(
                                      fontSize: 9,
                                      fontWeight: FontWeight.w800,
                                      color: Colors.white,
                                      letterSpacing: 0.2,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        );
                      },
                    ),
                  ),

                  // 3. Coin Balance (Extra Compact Wallet Style)
                  Container(
                    padding: const EdgeInsets.fromLTRB(8, 3, 3, 3),
                    decoration: BoxDecoration(
                      color: Colors.black.withOpacity(0.6),
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: const Color(0xFFFFD700).withOpacity(0.5), width: 1.0),
                      boxShadow: [
                         BoxShadow(
                           color: Colors.black.withOpacity(0.3),
                           blurRadius: 2,
                           offset: const Offset(0, 1),
                         )
                      ],
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(Icons.monetization_on_rounded, color: const Color(0xFFFFD700), size: 14, key: _coinIconKey),
                        const SizedBox(width: 5),
                        Consumer<AppProvider>(
                          builder: (context, provider, _) {
                            return AnimatedCoinBalance(
                              balance: provider.coins,
                              style: GoogleFonts.poppins(
                                fontSize: 11,
                                fontWeight: FontWeight.bold,
                                color: const Color(0xFFFFD700),
                                shadows: [
                                  Shadow(color: Colors.amber.withOpacity(0.5), blurRadius: 2)
                                ]
                              ),
                            );
                          },
                        ),
                        const SizedBox(width: 5),
                        Container(
                          width: 18,
                          height: 18,
                          decoration: const BoxDecoration(
                            gradient: LinearGradient(
                              colors: [Color(0xFF00E676), Color(0xFF00C853)],
                              begin: Alignment.topLeft,
                              end: Alignment.bottomRight,
                            ),
                            shape: BoxShape.circle,
                          ),
                          child: const Icon(Icons.add, color: Colors.white, size: 12),
                        )
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),

          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.all(16.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // 1. Promotional Banner (Game Center style)
                  _buildPromoBanner(),
                  const SizedBox(height: 24),

                  // 2. Daily Check-in Section
                  _buildDailyCheckIn(context),
                  const SizedBox(height: 24),

                  // 3. Games Section
                  _buildGamesSection(context),
                  const SizedBox(height: 24),

                  // 4. Daily Tasks Header
                  Text(
                    "Daily Tasks",
                    style: GoogleFonts.poppins(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                      color: Colors.white,
                    ),
                  ),
                  const SizedBox(height: 16),

                  // 4. Task List
                  Consumer<AppProvider>(
                    builder: (context, provider, child) {
                      return _buildTaskItem(
                        context,
                        title: "Watch Ads",
                        subtitle: "${provider.adsWatchedToday}/${provider.adDailyLimit}",
                        progress: provider.adDailyLimit > 0 
                            ? (provider.adsWatchedToday / provider.adDailyLimit).clamp(0.0, 1.0) 
                            : 0.0,
                        reward: provider.watchAdsReward,
                        icon: Icons.play_circle_fill,
                        iconColor: Colors.blueAccent,
                        actionLabel: "WATCH",
                        isLoading: _isWatchAdLoading,
                        onTap: () async {
                          if (provider.adsWatchedToday >= provider.adDailyLimit) {
                            showModalBottomSheet(
                              context: context,
                              backgroundColor: Colors.transparent,
                              isScrollControlled: true,
                              builder: (_) => LimitReachedSheet(
                                title: "Daily Ad Limit Reached",
                                message: "You have reached your daily ad watch limit.\nPlease come back tomorrow for more rewards!",
                                icon: Icons.videocam_off_outlined,
                                color: Colors.purpleAccent,
                              ),
                            );
                            return;
                          }

                          // Show Dialog First
                          showDialog(
                            context: context,
                            builder: (context) => RewardDialog(
                              rewardAmount: provider.watchAdsReward,
                              onClose: () => Navigator.pop(context),
                              onReceive: () async {
                                Navigator.pop(context); // Close dialog
                                
                                if (_isWatchAdLoading) return;

                                setState(() {
                                  _isWatchAdLoading = true;
                                });

                                try {
                                  bool success = await AdManager.showAdWithFallback(
                                    context,
                                    provider.watchAdsPriorities,
                                    () async {
                                       print("💎 Ad Callback Triggered");
                                       
                                       // 1. Add Coins (Source of Truth) - Do this first!
                                       try {
                                          await provider.addCoins(provider.watchAdsReward, source: 'ad_watch');
                                          print("💎 Coins Added to Provider");
                                          
                                          if (context.mounted) {
                                              ProfessionalToast.showSuccess(context, message: "You earned ${provider.watchAdsReward} coins!");
                                          }
                                       } catch (e) {
                                          print("❌ Error adding coins: $e");
                                       }

                                       // 2. Play Animation (Visual Only)
                                       // Small delay to let the ad close transition finish
                                       await Future.delayed(const Duration(milliseconds: 300));
                                       
                                       if (context.mounted) {
                                          print("💎 Starting Coin Animation");
                                          CoinAnimationOverlay.show(
                                            context, 
                                            _coinIconKey, 
                                            coinCount: 10,
                                            onComplete: () {
                                              print("💎 Coin Animation Complete");
                                            }
                                          );
                                       } else {
                                           print("⚠️ Context not mounted for animation");
                                       }
                                    }, 
                                  );
                                  
                                  if (!success && context.mounted) {
                                     ProfessionalToast.showError(context, message: "Ads not available");
                                  }

                                } finally {
                                  if (mounted) {
                                    setState(() {
                                      _isWatchAdLoading = false;
                                    });
                                  }
                                }
                              },
                            ),
                          );
                        },
                      );
                    },
                  ),
                  const SizedBox(height: 12),
                  Consumer<AppProvider>(
                    builder: (context, provider, child) {
                      int maxReward = provider.luckyWheelRewards.isNotEmpty 
                          ? provider.luckyWheelRewards.reduce((curr, next) => curr > next ? curr : next) 
                          : 100;
                          
                      return _buildTaskItem(
                        context,
                        title: "Play Lucky Wheel",
                        subtitle: "${provider.luckyWheelSpinsCount}/${provider.luckyWheelLimit}",
                        progress: provider.luckyWheelLimit > 0 
                            ? (provider.luckyWheelSpinsCount / provider.luckyWheelLimit).clamp(0.0, 1.0) 
                            : 0.0,
                        reward: maxReward,
                        icon: Icons.casino,
                        iconColor: Colors.orangeAccent,
                        actionLabel: "SPIN",
                        onTap: () {
                           if (provider.luckyWheelSpinsCount >= provider.luckyWheelLimit) {
                             showModalBottomSheet(
                               context: context,
                               backgroundColor: Colors.transparent,
                               isScrollControlled: true,
                               builder: (_) => LimitReachedSheet(
                                 title: "Daily Spin Limit Reached",
                                 message: "You have used all your lucky spins for today.\nPlease come back tomorrow!",
                                 icon: Icons.refresh,
                                 color: Colors.purpleAccent,
                               ),
                             );
                             return;
                           }

                           showDialog(
                             context: context,
                             builder: (context) => const LuckyWheelDialog(),
                           );
                        },
                      );
                    }
                  ),
                  const SizedBox(height: 12),
                  _buildTaskItem(
                    context,
                    title: "Refer & Earn",
                    reward: 500,
                    icon: Icons.share_rounded,
                    iconColor: Colors.purpleAccent,
                    actionLabel: "REFER",
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (context) => const ReferralScreen()),
                      );
                    },
                  ),
                  
                  const SizedBox(height: 100), // Bottom padding
                ],
              ),
            ),
          ),
        ],
      ),
      ),
    );
  }

  Widget _buildPromoBanner() {
    return Container(
      width: double.infinity,
      height: 140,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(16),
        gradient: const LinearGradient(
          colors: [Color(0xFF4A00E0), Color(0xFF8E2DE2)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF8E2DE2).withOpacity(0.4),
            blurRadius: 12,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: Stack(
        children: [
          Positioned(
            right: -20,
            top: -20,
            child: Icon(
              Icons.stars,
              size: 150,
              color: Colors.white.withOpacity(0.1),
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(20.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                  decoration: BoxDecoration(
                    color: Colors.amber,
                    borderRadius: BorderRadius.circular(4),
                  ),
                  child: Text(
                    "NEW SPENDER BONUS",
                    style: GoogleFonts.poppins(
                      fontSize: 10,
                      fontWeight: FontWeight.bold,
                      color: Colors.black,
                    ),
                  ),
                ),
                const SizedBox(height: 8),
                Text(
                  "Get Double Rewards!",
                  style: GoogleFonts.poppins(
                    fontSize: 20,
                    fontWeight: FontWeight.bold,
                    color: Colors.white,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  "Limited time offer",
                  style: GoogleFonts.poppins(
                    fontSize: 12,
                    color: Colors.white70,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    ).animate().fadeIn().slideX();
  }

  Widget _buildGamesSection(BuildContext context) {
    return Consumer<AppProvider>(
      builder: (context, provider, child) {
        final games = provider.games;
        // Logic: Show max 7 games + 1 'More' button if needed
        final bool showMoreButton = games.length > 7;
        final int displayCount = showMoreButton ? 7 : games.length;
        // Total items in grid: displayCount + (1 if showMoreButton)
        final int totalItems = displayCount + (showMoreButton ? 1 : 0);

        return Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 4.0),
              child: Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(6),
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: const Icon(Icons.gamepad_rounded, color: Colors.white, size: 18),
                  ),
                  const SizedBox(width: 10),
                  Text(
                    "Games",
                    style: GoogleFonts.poppins(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                      color: Colors.white,
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 12),
            if (games.isEmpty)
              Container(
                width: double.infinity,
                padding: const EdgeInsets.symmetric(vertical: 30),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.05),
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: Colors.white.withOpacity(0.05)),
                ),
                child: Column(
                  children: [
                    const Icon(Icons.videogame_asset_off, color: Colors.white24, size: 40),
                    const SizedBox(height: 8),
                    Text(
                      "No games available",
                      style: GoogleFonts.poppins(
                        fontSize: 14,
                        color: Colors.white54,
                      ),
                    ),
                  ],
                ),
              )
            else
              GridView.builder(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                padding: EdgeInsets.zero,
                gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                  crossAxisCount: 4,
                  childAspectRatio: 0.75,
                  crossAxisSpacing: 10,
                  mainAxisSpacing: 12,
                ),
                itemCount: totalItems,
                itemBuilder: (context, index) {
                  // If it's the last item and we need to show 'More' button
                  if (showMoreButton && index == displayCount) {
                    return GestureDetector(
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(builder: (context) => const AllGamesScreen()),
                        );
                      },
                      child: Column(
                        children: [
                          Expanded(
                            child: Container(
                              decoration: BoxDecoration(
                                gradient: LinearGradient(
                                  colors: [
                                    const Color(0xFF6C63FF).withOpacity(0.2),
                                    const Color(0xFF6C63FF).withOpacity(0.1),
                                  ],
                                  begin: Alignment.topLeft,
                                  end: Alignment.bottomRight,
                                ),
                                borderRadius: BorderRadius.circular(18),
                                border: Border.all(color: const Color(0xFF6C63FF).withOpacity(0.3)),
                                boxShadow: [
                                  BoxShadow(
                                    color: const Color(0xFF6C63FF).withOpacity(0.1),
                                    blurRadius: 8,
                                    offset: const Offset(0, 4),
                                  ),
                                ],
                              ),
                              child: Center(
                                child: Container(
                                  padding: const EdgeInsets.all(12),
                                  decoration: BoxDecoration(
                                    color: const Color(0xFF6C63FF).withOpacity(0.2),
                                    shape: BoxShape.circle,
                                  ),
                                  child: const Icon(
                                    Icons.arrow_forward_rounded,
                                    color: Colors.white,
                                    size: 24,
                                  ),
                                ),
                              ),
                            ),
                          ),
                          const SizedBox(height: 8),
                          Text(
                            "View All",
                            textAlign: TextAlign.center,
                            style: GoogleFonts.poppins(
                              fontSize: 11,
                              color: Colors.white,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                        ],
                      ).animate().scale(),
                    );
                  }

                  // Normal Game Item
                  final game = games[index];
                  return GestureDetector(
                    onTap: () async {
                      final provider = Provider.of<AppProvider>(context, listen: false);
                      
                      // Ensure daily limit is reset if it's a new day
                      await provider.checkDailyLimitReset();
                      
                      // Check Daily Limit
                      if (provider.gamesPlayedToday >= provider.gameDailyLimit) {
                         showModalBottomSheet(
                           context: context,
                           backgroundColor: Colors.transparent,
                           isScrollControlled: true,
                           builder: (_) => LimitReachedSheet(
                             title: "Daily Game Limit Reached",
                             message: "You have reached your daily game limit.\nPlease come back tomorrow!",
                             icon: Icons.timer_off_outlined,
                             color: Colors.orange,
                           ),
                         );
                         return;
                      }

                      final result = await Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => GameWebViewScreen(
                            url: game.url,
                            title: game.title,
                            rewardAmount: game.winReward > 0 ? game.winReward : 50,
                            durationSeconds: game.playTime > 0 ? game.playTime : 60,
                          ),
                        ),
                      );

                      if (result != null && result is Map && mounted) {
                        final bool rewardClaimed = result['rewardClaimed'] ?? false;
                        final bool isTimerComplete = result['isTimerComplete'] ?? false;
                        final int playedSeconds = result['playedSeconds'] ?? 0;
                        final int requiredSeconds = game.playTime > 0 ? game.playTime : 60;
                        
                        if (isTimerComplete && !rewardClaimed) {
                           _showGameRewardDialog(context, game.winReward > 0 ? game.winReward : 50, game.id.toString());
                        } else if (!rewardClaimed && !isTimerComplete && playedSeconds < requiredSeconds) {
                          showDialog(
                            context: context,
                            builder: (context) => GameWarningDialog(
                              playedSeconds: playedSeconds,
                              requiredSeconds: requiredSeconds,
                              rewardAmount: game.winReward > 0 ? game.winReward : 50,
                              onClose: () => Navigator.pop(context),
                            ),
                          );
                        }
                      }
                    },
                    child: Column(
                      children: [
                        Expanded(
                          child: Container(
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(18),
                              boxShadow: [
                                BoxShadow(
                                  color: Colors.black.withOpacity(0.3),
                                  blurRadius: 8,
                                  offset: const Offset(0, 4),
                                ),
                              ],
                            ),
                            child: ClipRRect(
                              borderRadius: BorderRadius.circular(18),
                              child: CachedNetworkImage(
                                imageUrl: game.image,
                                fit: BoxFit.cover,
                                width: double.infinity,
                                height: double.infinity,
                                placeholder: (context, url) => Container(
                                  color: Colors.white10,
                                  child: const Center(child: Icon(Icons.gamepad, color: Colors.white24)),
                                ),
                                errorWidget: (context, url, error) => Container(
                                  color: Colors.white10,
                                  child: const Center(child: Icon(Icons.error, color: Colors.white24)),
                                ),
                              ),
                            ),
                          ),
                        ),
                        const SizedBox(height: 8),
                        Text(
                          game.title,
                          textAlign: TextAlign.center,
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                          style: GoogleFonts.poppins(
                            fontSize: 11,
                            color: Colors.white,
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      ],
                    ).animate(delay: (30 * index).ms).fadeIn(),
                  );
                },
              ),
          ],
        );
      },
    );
  }

  void _showCheckInDialog(BuildContext context, AppProvider provider) {
    // Find the reward amount for the current day
    final currentRewardItem = provider.dailyRewards.firstWhere(
      (item) => item['day'] == provider.currentDay, 
      orElse: () => {'reward': {'coins': 0}},
    );
    final coins = currentRewardItem != null && currentRewardItem['reward'] != null 
        ? (currentRewardItem['reward']['coins'] ?? 0)
        : 0;

    showDialog(
      context: context,
      barrierDismissible: true,
      builder: (ctx) => RewardDialog(
        rewardAmount: coins,
        currencySymbol: provider.currencySymbol,
        coinRate: provider.coinRate,
        onReceive: () async {
            final adService = GoogleAdService();
            
            // 1. INSTANT CHECK (Fast Path)
            // If ANY ad is ready, proceed immediately.
            if (adService.isRewardedAdReady() || adService.isInterstitialAdReady()) {
               if (context.mounted) {
                 Navigator.pop(ctx);
                 _handleClaimReward(context, provider, coins, showLoading: false);
               }
               return;
            }

            // 2. PARALLEL LOAD (Smart Wait)
            // If no ads ready, try loading BOTH in parallel and wait for the first one.
            debugPrint("⏳ No ads ready. Racing Rewarded vs Interstitial...");
            
            try {
              // Trigger both loads
              final rewardFuture = adService.loadRewardedAd(context);
              final interFuture = adService.loadInterstitialAd(context);
              
              // Wait max 5 seconds for EITHER to be ready
              // We check periodically to return as soon as ONE is ready
              int checkCount = 0;
              bool foundAd = false;
              
              while (checkCount < 10) { // 10 * 500ms = 5 seconds max
                await Future.delayed(const Duration(milliseconds: 500));
                if (adService.isRewardedAdReady() || adService.isInterstitialAdReady()) {
                  foundAd = true;
                  break;
                }
                checkCount++;
              }
              
              if (foundAd) {
                 debugPrint("✅ Ad found during wait!");
              } else {
                 debugPrint("⚠️ Timed out waiting for ads (5s).");
              }
            } catch (e) {
              debugPrint("❌ Error waiting for ads: $e");
            }

            // 3. FINAL CHECK
            if (context.mounted) {
              if (adService.isRewardedAdReady() || adService.isInterstitialAdReady()) {
                  Navigator.pop(ctx); // Close RewardDialog
                  _handleClaimReward(context, provider, coins, showLoading: false);
              } else {
                  ProfessionalToast.showError(context, message: "Ads not available");
                  debugPrint("❌ Final Status: No Ads ready after wait.");
              }
            }
          },
        onClose: () => Navigator.pop(ctx),
      ),
    );
  }

  void _handleClaimReward(BuildContext context, AppProvider provider, int rewardAmount, {bool showLoading = true}) async {
    // Show Loading only if requested
    if (showLoading) {
      showDialog(
        context: context,
        barrierDismissible: false,
        builder: (_) => const Center(child: CircularProgressIndicator()),
      );
      // Wait a bit to ensure UI updates
      await Future.delayed(const Duration(milliseconds: 500));
      // Close Loading
      if (context.mounted) Navigator.pop(context);
    }

    if (!context.mounted) return;

    // Show Ad
    bool adShown = await AdManager.showAdWithFallback(
      context,
      provider.dailyRewardAdPriorities,
      () async {
        // On Ad Success
        final success = await provider.claimDailyReward();
        if (success && context.mounted) {
           // Show Animations
           CoinAnimationOverlay.show(context, _coinIconKey);
           ProfessionalToast.show(context, coinAmount: rewardAmount);
        }
      }
    );

    if (!adShown && context.mounted) {
      ProfessionalToast.showError(context, message: "Ads not available");
    }
  }

  Widget _buildDailyCheckIn(BuildContext context) {
    return Consumer<AppProvider>(
      builder: (context, provider, child) {
        final rewards = provider.dailyRewards;
        final bool isLoading = provider.isDailyRewardLoading && rewards.isEmpty;
        
        return Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: const Color(0xFF1F1B2E),
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: Colors.white.withOpacity(0.05)),
          ),
          child: isLoading 
            ? const Center(child: Padding(padding: EdgeInsets.all(20), child: CircularProgressIndicator()))
            : Column(
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        "Daily Check-in",
                        style: GoogleFonts.poppins(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                          color: Colors.white,
                        ),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        "Get coins every day!",
                        style: GoogleFonts.poppins(
                          fontSize: 12,
                          color: Colors.white54,
                        ),
                      ),
                    ],
                  ),
                  GestureDetector(
                    onTap: () {
                      if (!provider.canClaimDailyReward) return;
                      _showCheckInDialog(context, provider);
                    },
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                      decoration: BoxDecoration(
                        gradient: provider.canClaimDailyReward 
                          ? const LinearGradient(colors: [Colors.orange, Colors.deepOrange])
                          : LinearGradient(colors: [Colors.grey.shade700, Colors.grey.shade800]),
                        borderRadius: BorderRadius.circular(20),
                        boxShadow: provider.canClaimDailyReward ? [
                          BoxShadow(
                            color: Colors.orange.withOpacity(0.4),
                            blurRadius: 8,
                            offset: const Offset(0, 4),
                          ),
                        ] : [],
                      ),
                      child: Text(
                        provider.canClaimDailyReward ? "Check In" : "Checked In",
                        style: GoogleFonts.poppins(
                          fontWeight: FontWeight.bold,
                          color: Colors.white,
                        ),
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 16),
              // Days Row
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: rewards.isEmpty 
                  // Fallback Mock UI if no data
                  ? List.generate(7, (index) => _buildDayItem(index + 1, (index + 1) * 10, 'locked'))
                  : rewards.map((item) {
                      final day = item['day'];
                      final reward = item['reward'];
                      final coins = reward != null ? (reward['coins'] ?? 0) : 0;
                      final status = item['status'] ?? 'locked';
                      return _buildDayItem(day, coins, status);
                    }).toList(),
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _buildDayItem(int day, int coins, String status) {
    final isClaimed = status == 'claimed';
    final isClaimable = status == 'claimable';
    final isLocked = status == 'locked';

    Color bgColor = Colors.white.withOpacity(0.1);
    Color borderColor = Colors.transparent;
    
    if (isClaimed) {
      bgColor = Colors.green.withOpacity(0.2);
      borderColor = Colors.green;
    } else if (isClaimable) {
      bgColor = Colors.amber;
      borderColor = Colors.orange;
    }

    return Column(
      children: [
        Container(
          width: 36,
          height: 36,
          decoration: BoxDecoration(
            color: bgColor,
            shape: BoxShape.circle,
            border: (isClaimable || isClaimed) ? Border.all(color: borderColor, width: 2) : null,
          ),
          child: Center(
            child: isClaimed
                ? const Icon(Icons.check, size: 20, color: Colors.green)
                : Text(
                    "+$coins",
                    style: GoogleFonts.poppins(
                      fontSize: 10,
                      fontWeight: FontWeight.bold,
                      color: isClaimable ? Colors.black : Colors.white54,
                    ),
                  ),
          ),
        ),
        const SizedBox(height: 4),
        Text(
          "Day $day",
          style: GoogleFonts.poppins(
            fontSize: 10,
            color: isClaimable ? Colors.white : Colors.white38,
          ),
        ),
      ],
    );
  }

  Widget _buildTaskItem(
    BuildContext context, {
    required String title,
    String? subtitle,
    double? progress,
    required int reward,
    required IconData icon,
    required Color iconColor,
    required String actionLabel,
    required VoidCallback onTap,
    bool isLoading = false,
  }) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: const Color(0xFF1F1B2E),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.white.withOpacity(0.05)),
      ),
      child: Row(
        children: [
          // Icon Box
          Container(
            width: 50,
            height: 50,
            decoration: BoxDecoration(
              color: iconColor.withOpacity(0.1),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Icon(icon, color: iconColor, size: 28),
          ),
          const SizedBox(width: 16),
          
          // Details
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: GoogleFonts.poppins(
                    fontSize: 15,
                    fontWeight: FontWeight.w600,
                    color: Colors.white,
                  ),
                ),
                if (progress != null) ...[
                  const SizedBox(height: 6),
                  // Progress Bar
                  ClipRRect(
                    borderRadius: BorderRadius.circular(4),
                    child: LinearProgressIndicator(
                      value: progress,
                      backgroundColor: Colors.white10,
                      valueColor: AlwaysStoppedAnimation<Color>(
                        progress == 1.0 ? Colors.green : Colors.blue,
                      ),
                      minHeight: 6,
                    ),
                  ),
                ],
                if (subtitle != null) ...[
                  const SizedBox(height: 4),
                  Row(
                    children: [
                      Text(
                        subtitle,
                        style: GoogleFonts.poppins(
                          fontSize: 11,
                          color: Colors.white54,
                        ),
                      ),
                    ],
                  ),
                ],
              ],
            ),
          ),

          const SizedBox(width: 12),

          // Reward & Action
          Column(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                decoration: BoxDecoration(
                  color: Colors.amber.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(4),
                  border: Border.all(color: Colors.amber.withOpacity(0.3)),
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    const Icon(Icons.monetization_on, color: Colors.amber, size: 12),
                    const SizedBox(width: 4),
                    Text(
                      "+$reward",
                      style: GoogleFonts.poppins(
                        fontSize: 12,
                        fontWeight: FontWeight.bold,
                        color: Colors.amber,
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 8),
              InkWell(
                onTap: onTap,
                borderRadius: BorderRadius.circular(20),
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
                  constraints: const BoxConstraints(minWidth: 60),
                  decoration: BoxDecoration(
                    gradient: const LinearGradient(
                      colors: [Color(0xFFFF7551), Color(0xFFFF512F)],
                    ),
                    borderRadius: BorderRadius.circular(20),
                    boxShadow: [
                      BoxShadow(
                        color: const Color(0xFFFF512F).withOpacity(0.4),
                        blurRadius: 6,
                        offset: const Offset(0, 3),
                      ),
                    ],
                  ),
                  child: isLoading
                      ? const SizedBox(
                          width: 16,
                          height: 16,
                          child: CircularProgressIndicator(
                            strokeWidth: 2,
                            valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                          ),
                        )
                      : Text(
                          actionLabel,
                          style: GoogleFonts.poppins(
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                            color: Colors.white,
                          ),
                        ),
                ),
              ),
            ],
          ),
        ],
      ),
    ).animate().fadeIn(duration: 400.ms).slideY(begin: 0.1, end: 0);
  }
}
