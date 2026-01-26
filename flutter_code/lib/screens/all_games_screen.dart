import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import '../providers/app_provider.dart';
import '../providers/language_provider.dart';
import 'game_webview_screen.dart';
import '../dialog/game_reward_dialog.dart';
import '../dialog/game_warning_dialog.dart';
import '../dialog/game_limit_dialog.dart';
import '../dialog/limit_reached_sheet.dart';
import '../widgets/toast/professional_toast.dart';
import '../widgets/coin_animation_overlay.dart';
import '../providers/ad_provider.dart';
import '../services/ad_manager_service.dart';
import '../widgets/animated_coin_balance.dart';
import '../utils/app_theme.dart';

class AllGamesScreen extends StatefulWidget {
  const AllGamesScreen({super.key});

  @override
  State<AllGamesScreen> createState() => _AllGamesScreenState();
}

class _AllGamesScreenState extends State<AllGamesScreen> {
  final GlobalKey _coinIconKey = GlobalKey();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<AppProvider>(context, listen: false).fetchGames();
    });
  }

  Future<void> _showGameRewardDialog(BuildContext context, int rewardAmount, int gameId) async {
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
             ProfessionalToast.showError(context, message: Provider.of<LanguageProvider>(context, listen: false).getText('ads_unavailable'));
          }
        },
        onClose: () {
          Navigator.pop(ctx);
        },
      ),
    );
  }

  Future<void> _claimGameReward(BuildContext context, int baseReward, int multiplier, int gameId) async {
    final provider = Provider.of<AppProvider>(context, listen: false);
    final totalReward = baseReward * multiplier;
    
    // Show animation first, then add coins
    if (context.mounted) {
      CoinAnimationOverlay.show(
        context, 
        _coinIconKey, 
        coinCount: 10,
        onComplete: () async {
           await provider.addCoins(totalReward, source: 'game_play', gameId: gameId.toString());
           print("🎮 [Game Play] Coins Credited: $totalReward | Games Played Today: ${provider.gamesPlayedToday} | Daily Limit: ${provider.gameDailyLimit}");
           if (context.mounted) {
             final lang = Provider.of<LanguageProvider>(context, listen: false);
             ProfessionalToast.showSuccess(context, message: "${lang.getText('earned_coins_msg')} $totalReward ${lang.getText('coins')}!");
           }
        }
      );
    }
  }



  @override
  Widget build(BuildContext context) {
    final langProvider = Provider.of<LanguageProvider>(context);
    return Scaffold(
      backgroundColor: AppTheme.darkBackgroundColor,
      appBar: AppBar(
        backgroundColor: AppTheme.darkBackgroundColor,
        elevation: 0,
        flexibleSpace: Container(
          decoration: AppTheme.backgroundDecoration,
        ),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new, color: Colors.white),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          "All Games",
          style: GoogleFonts.poppins(
            fontSize: 20,
            fontWeight: FontWeight.bold,
            color: Colors.white,
          ),
        ),
        actions: [
             Container(
                key: _coinIconKey,
                margin: const EdgeInsets.only(right: 16),
                child: Consumer<AppProvider>(
                  builder: (context, provider, child) {
                     return Row(
                       children: [
                         const Icon(Icons.monetization_on, color: Colors.amber, size: 20),
                         const SizedBox(width: 4),
                         AnimatedCoinBalance(
                           balance: provider.coins,
                           style: GoogleFonts.poppins(color: Colors.white, fontWeight: FontWeight.bold),
                         ),
                       ],
                     );
                  }
                ),
             )
        ],
      ),
      body: Consumer<AppProvider>(
        builder: (context, provider, child) {
          final games = provider.games;

          if (games.isEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(Icons.videogame_asset_off, color: Colors.white24, size: 64),
                  const SizedBox(height: 16),
                  Text(
                    langProvider.getText('no_games_found'),
                    style: GoogleFonts.poppins(color: Colors.white54, fontSize: 16),
                  ),
                ],
              ),
            );
          }

          return GridView.builder(
            padding: const EdgeInsets.all(16),
            physics: const BouncingScrollPhysics(),
            gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
              crossAxisCount: 3,
              childAspectRatio: 0.75,
              crossAxisSpacing: 12,
              mainAxisSpacing: 16,
            ),
            itemCount: games.length,
            itemBuilder: (context, index) {
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
                         title: langProvider.getText('daily_game_limit'),
                         message: langProvider.getText('game_limit_msg'),
                         icon: Icons.timer_off_outlined,
                         color: Colors.orange,
                       ),
                     );
                     return;
                  }

                  // Increment Count (Optimistic - Fire and forget to avoid UI delay)
                  provider.incrementGamePlayCount();

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
                    
                    if (!rewardClaimed) {
                        if (isTimerComplete) {
                            _showGameRewardDialog(context, game.winReward > 0 ? game.winReward : 50, game.id);
                        } else if (playedSeconds < requiredSeconds) {
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
                  }
                },
                child: Column(
                  children: [
                    Expanded(
                      child: Container(
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(12),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withOpacity(0.3),
                              blurRadius: 8,
                              offset: const Offset(0, 4),
                            ),
                          ],
                        ),
                        child: ClipRRect(
                          borderRadius: BorderRadius.circular(12),
                          child: CachedNetworkImage(
                            imageUrl: game.image,
                            fit: BoxFit.cover,
                            width: double.infinity,
                            height: double.infinity,
                            memCacheHeight: 400, // Optimize for grid items
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
                        fontSize: 12,
                        color: Colors.white,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                  ],
                ).animate(delay: (30 * index).ms).fadeIn().scale(),
              );
            },
          );
        },
      ),
    );
  }
}
