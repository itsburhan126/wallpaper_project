import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:url_launcher/url_launcher.dart';
import '../providers/app_provider.dart';
import '../services/ad_manager_service.dart';
import '../utils/constants.dart';
import '../dialog/reward_dialog.dart';
import '../widgets/coin_animation_overlay.dart';
import '../services/google_ad_service.dart';
import '../widgets/toast/professional_toast.dart';
import 'all_games_screen.dart';

class TaskScreen extends StatefulWidget {
  const TaskScreen({super.key});

  @override
  State<TaskScreen> createState() => _TaskScreenState();
}

class _TaskScreenState extends State<TaskScreen> {
  final GlobalKey _coinIconKey = GlobalKey();

  @override
  void initState() {
    super.initState();
    // Fetch user coin balance and games
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<AppProvider>(context, listen: false).fetchUserBalance();
      Provider.of<AppProvider>(context, listen: false).fetchGames();
    });
  }

  @override
  Widget build(BuildContext context) {
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
            expandedHeight: 100.0,
            floating: true,
            pinned: true,
            elevation: 0,
            flexibleSpace: FlexibleSpaceBar(
              background: Container(
                decoration: const BoxDecoration(
                  gradient: LinearGradient(
                    colors: [Color(0xFF2A1B4E), Color(0xFF120C24)],
                    begin: Alignment.topCenter,
                    end: Alignment.bottomCenter,
                  ),
                ),
              ),
              titlePadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
              title: Row(
                children: [
                  // Avatar
                  Container(
                    width: 40,
                    height: 40,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      border: Border.all(color: Colors.amber, width: 2),
                      color: Colors.deepPurpleAccent,
                      boxShadow: [
                        BoxShadow(
                          color: Colors.amber.withOpacity(0.3),
                          blurRadius: 8,
                          spreadRadius: 1,
                        )
                      ],
                    ),
                    child: const Icon(Icons.person, color: Colors.white, size: 24),
                  ),
                  const SizedBox(width: 12),
                  // User Info
                  Expanded(
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          "Guest User", // Dynamic name later
                          style: GoogleFonts.poppins(
                            fontSize: 14,
                            fontWeight: FontWeight.bold,
                            color: Colors.white,
                          ),
                        ),
                        Row(
                          children: [
                            const Icon(Icons.shield, color: Colors.amber, size: 12),
                            const SizedBox(width: 4),
                            Text(
                              "Level 1",
                              style: GoogleFonts.poppins(
                                fontSize: 10,
                                color: Colors.white70,
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                  // Coin Balance
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                    decoration: BoxDecoration(
                      color: Colors.black45,
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: Colors.amber.withOpacity(0.5)),
                    ),
                    child: Row(
                      children: [
                        Icon(Icons.monetization_on, color: Colors.amber, size: 16, key: _coinIconKey),
                        const SizedBox(width: 6),
                        Consumer<AppProvider>(
                          builder: (context, provider, _) {
                            return Text(
                              "${provider.coins}",
                              style: GoogleFonts.poppins(
                                fontSize: 14,
                                fontWeight: FontWeight.bold,
                                color: Colors.amber,
                              ),
                            );
                          },
                        ),
                        const SizedBox(width: 4),
                        Container(
                          width: 16,
                          height: 16,
                          decoration: const BoxDecoration(
                            color: Colors.amber,
                            shape: BoxShape.circle,
                          ),
                          child: const Icon(Icons.add, color: Colors.black, size: 12),
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
                  _buildTaskItem(
                    context,
                    title: "Watch Ads",
                    subtitle: "1/6",
                    progress: 0.16,
                    reward: 10,
                    icon: Icons.play_circle_fill,
                    iconColor: Colors.blueAccent,
                    actionLabel: "7S",
                    onTap: () {
                      // Trigger Ad
                    },
                  ),
                  const SizedBox(height: 12),
                  _buildTaskItem(
                    context,
                    title: "Play Lucky Wheel",
                    subtitle: "0/5",
                    progress: 0.0,
                    reward: 50,
                    icon: Icons.casino,
                    iconColor: Colors.orangeAccent,
                    actionLabel: "GO",
                    onTap: () {
                      // Navigate to Wheel
                    },
                  ),
                  const SizedBox(height: 12),
                  _buildTaskItem(
                    context,
                    title: "Play Lucky Slots",
                    subtitle: "0/5",
                    progress: 0.0,
                    reward: 100,
                    icon: Icons.games, // Replaced slot_machine with games
                    // Fallback icon if slot_machine not available
                    // icon: Icons.videogame_asset, 
                    iconColor: Colors.greenAccent,
                    actionLabel: "GO",
                    onTap: () {
                      // Navigate to Slots
                    },
                  ),
                   const SizedBox(height: 12),
                  _buildTaskItem(
                    context,
                    title: "Invite Friends",
                    subtitle: "0/10",
                    progress: 0.0,
                    reward: 500,
                    icon: Icons.group_add,
                    iconColor: Colors.purpleAccent,
                    actionLabel: "INVITE",
                    onTap: () {
                      // Share
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
                      if (await canLaunchUrl(Uri.parse(game.url))) {
                        await launchUrl(Uri.parse(game.url), mode: LaunchMode.externalApplication);
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
            // Check if Ad is ready, if not load it while showing loading spinner
            bool isReady = GoogleAdService().isRewardedAdReady();
            if (!isReady) {
               // Load Rewarded Ad with timeout
               debugPrint("⏳ Rewarded Ad not ready, attempting to load with 15s timeout...");
               try {
                 isReady = await GoogleAdService().loadRewardedAd(context).timeout(
                    const Duration(seconds: 15), 
                    onTimeout: () {
                      debugPrint("⏰ Rewarded Ad Load Timeout (15s)");
                      return false;
                    }
                 );
               } catch (e) {
                 debugPrint("❌ Rewarded Ad Load Error: $e");
                 isReady = false;
               }

               // Fallback: If Rewarded failed to load, try Interstitial
               if (!isReady) {
                  debugPrint("⚠️ Rewarded Ad failed. Checking Interstitial fallback...");
                  if (GoogleAdService().isInterstitialAdReady()) {
                     isReady = true;
                     debugPrint("✅ Interstitial Ad is ready as fallback.");
                  } else {
                     debugPrint("⏳ Interstitial not ready, attempting to load...");
                     isReady = await GoogleAdService().loadInterstitialAd(context);
                  }
               }
            }
            
            if (context.mounted) {
              Navigator.pop(ctx); // Close Dialog
              
              if (isReady) {
                  // Show Ad immediately (AdManager will handle Rewarded vs Interstitial logic)
                  _handleClaimReward(context, provider, coins, showLoading: false);
              } else {
                  // Ad failed to load
                  ProfessionalToast.showError(context, message: "Ads not available right now. Please try again.");
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
      ProfessionalToast.showError(context, message: "Ads not ready. Please try again later.");
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
    required String subtitle,
    required double progress,
    required int reward,
    required IconData icon,
    required Color iconColor,
    required String actionLabel,
    required VoidCallback onTap,
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
                  child: Text(
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
