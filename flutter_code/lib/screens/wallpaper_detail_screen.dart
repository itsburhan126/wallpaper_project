import 'dart:typed_data';
import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:provider/provider.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:dio/dio.dart';
import 'package:image_gallery_saver_plus/image_gallery_saver_plus.dart';
import 'package:permission_handler/permission_handler.dart';
import 'package:flutter/services.dart'; // Add this for MissingPluginException
import 'package:async_wallpaper/async_wallpaper.dart';
import 'package:flutter_cache_manager/flutter_cache_manager.dart';
import '../models/wallpaper_model.dart';
import '../providers/app_provider.dart';
import '../providers/language_provider.dart';
import '../providers/ad_provider.dart';
import '../utils/constants.dart';
import '../utils/app_theme.dart';
import '../services/ad_manager_service.dart';
import '../widgets/ads/universal_banner_ad.dart';
import '../widgets/toast/professional_toast.dart'; // Add this import

class WallpaperDetailScreen extends StatefulWidget {
  final Wallpaper wallpaper;
  final String? heroTag;

  const WallpaperDetailScreen({super.key, required this.wallpaper, this.heroTag});

  @override
  State<WallpaperDetailScreen> createState() => _WallpaperDetailScreenState();
}

class _WallpaperDetailScreenState extends State<WallpaperDetailScreen> with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  bool _isRewardReady = false;

  @override
  void initState() {
    super.initState();
    
    // Get duration from provider
    final provider = Provider.of<AppProvider>(context, listen: false);
    
    // Track View
    provider.viewWallpaper(widget.wallpaper.id);

    final durationSeconds = provider.wallpaperViewTime;

    _controller = AnimationController(
      vsync: this,
      duration: Duration(seconds: durationSeconds > 0 ? durationSeconds : 10), // Default fallback
    );

    _controller.forward().whenComplete(() {
      if (mounted) {
        setState(() {
          _isRewardReady = true;
        });
      }
    });
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  void _showCoinDialog() {
    final languageProvider = Provider.of<LanguageProvider>(context, listen: false);
    showDialog(
      context: context,
      barrierColor: Colors.black.withOpacity(0.8),
      builder: (context) => Dialog(
        backgroundColor: Colors.transparent,
        insetPadding: const EdgeInsets.symmetric(horizontal: 24),
        child: Stack(
          clipBehavior: Clip.none,
          alignment: Alignment.center,
          children: [
            Container(
              width: double.infinity,
              padding: const EdgeInsets.fromLTRB(24, 40, 24, 24),
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: [
                    const Color(0xFF2A2D3E),
                    const Color(0xFF1F1B2E),
                  ],
                ),
                borderRadius: BorderRadius.circular(28),
                border: Border.all(color: Colors.white.withOpacity(0.1), width: 1),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.5),
                    blurRadius: 30,
                    spreadRadius: 10,
                  ),
                ],
              ),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  // Icon with Glow
                  Container(
                    width: 80,
                    height: 80,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: Colors.amber.withOpacity(0.1),
                      border: Border.all(color: Colors.amber.withOpacity(0.3), width: 2),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.amber.withOpacity(0.2),
                          blurRadius: 20,
                          spreadRadius: 5,
                        ),
                      ],
                    ),
                    child: const Icon(
                      Icons.monetization_on_rounded,
                      size: 48,
                      color: Colors.amber,
                    ).animate(onPlay: (c) => c.repeat(reverse: true))
                     .scale(duration: 1500.ms, begin: const Offset(1.0, 1.0), end: const Offset(1.1, 1.1))
                     .shimmer(duration: 2000.ms, color: Colors.white.withOpacity(0.5)),
                  ),
                  const SizedBox(height: 24),
                  
                  // Title
                  Text(
                    languageProvider.getText('earn_coins'),
                    textAlign: TextAlign.center,
                    style: GoogleFonts.poppins(
                      fontSize: 24,
                      fontWeight: FontWeight.bold,
                      color: Colors.white,
                      letterSpacing: 0.5,
                    ),
                  ),
                  const SizedBox(height: 12),
                  
                  // Description
                  Text(
                    languageProvider.getText('watch_ad_text'),
                    textAlign: TextAlign.center,
                    style: GoogleFonts.poppins(
                      fontSize: 14,
                      color: Colors.white70,
                      height: 1.5,
                    ),
                  ),
                  const SizedBox(height: 32),
                  
                  // Watch Button
                  Container(
                    width: double.infinity,
                    height: 56,
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(16),
                      gradient: const LinearGradient(
                        colors: [Color(0xFFFFC107), Color(0xFFFF9800)],
                      ),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.amber.withOpacity(0.4),
                          blurRadius: 8,
                          offset: const Offset(0, 4),
                        ),
                      ],
                    ),
                    child: ElevatedButton(
                      onPressed: () {
                        Navigator.pop(context);
                        _watchAd();
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.transparent,
                        foregroundColor: Colors.black,
                        elevation: 0,
                        shadowColor: Colors.transparent,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(16),
                        ),
                      ),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          const Icon(Icons.play_circle_filled_rounded, size: 28, color: Colors.black87),
                          const SizedBox(width: 12),
                          Text(
                            languageProvider.getText('watch_ad_btn').toUpperCase(),
                            style: GoogleFonts.poppins(
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                              color: Colors.black87,
                              letterSpacing: 1,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ).animate().shimmer(delay: 1000.ms, duration: 2000.ms),
                  
                  const SizedBox(height: 16),
                  
                  // Cancel Button
                  TextButton(
                    onPressed: () => Navigator.pop(context),
                    child: Text(
                      languageProvider.getText('cancel') == 'cancel' ? 'Maybe Later' : languageProvider.getText('cancel'),
                      style: GoogleFonts.poppins(
                        color: Colors.white38,
                        fontSize: 14,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                  ),
                ],
              ),
            ).animate().slideY(begin: 0.1, end: 0, duration: 400.ms, curve: Curves.easeOutBack).fadeIn(duration: 400.ms),

            // Close Button
            Positioned(
              top: 10,
              right: 10,
              child: Material(
                color: Colors.transparent,
                child: InkWell(
                  onTap: () => Navigator.pop(context),
                  borderRadius: BorderRadius.circular(20),
                  child: Container(
                    padding: const EdgeInsets.all(8),
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: Colors.white.withOpacity(0.1),
                    ),
                    child: const Icon(Icons.close, color: Colors.white70, size: 20),
                  ),
                ),
              ),
            ).animate().scale(delay: 200.ms, duration: 300.ms, curve: Curves.elasticOut),
          ],
        ),
      ),
    );
  }

  void _watchAd() async {
    final languageProvider = Provider.of<LanguageProvider>(context, listen: false);
    // Show Loading
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (_) => const Center(child: CircularProgressIndicator()),
    );

    // Use priorities from AdProvider
    final adProvider = Provider.of<AdProvider>(context, listen: false);
    final fallbacks = adProvider.adPriorities.isNotEmpty ? adProvider.adPriorities : ['admob', 'facebook', 'unity'];
    
    // Close Loading
    if (mounted) Navigator.pop(context);

    bool success = await AdManager.showAdWithFallback(
      context, 
      fallbacks, 
      () {
        // On Ad Closed & Rewarded
        Provider.of<AppProvider>(context, listen: false).addCoins(10);
        _showCongratulationDialog();
      }
    );

    if (!success) {
      // Optional: Show error dialog or snackbar
       ProfessionalToast.showError(context, message: languageProvider.getText('ad_load_failed'));
    }
  }

  void _showCongratulationDialog() {
    final languageProvider = Provider.of<LanguageProvider>(context, listen: false);
    showDialog(
      context: context,
      barrierColor: Colors.black.withOpacity(0.8),
      builder: (context) => Dialog(
        backgroundColor: Colors.transparent,
        insetPadding: const EdgeInsets.symmetric(horizontal: 24),
        child: Stack(
          clipBehavior: Clip.none,
          alignment: Alignment.center,
          children: [
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(32),
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: [
                    const Color(0xFF2A2D3E),
                    const Color(0xFF1F1B2E),
                  ],
                ),
                borderRadius: BorderRadius.circular(28),
                border: Border.all(color: Colors.green.withOpacity(0.3), width: 1),
                boxShadow: [
                  BoxShadow(
                    color: Colors.green.withOpacity(0.2),
                    blurRadius: 30,
                    spreadRadius: 5,
                  ),
                ],
              ),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  // Check Icon
                  Container(
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: Colors.green.withOpacity(0.1),
                      border: Border.all(color: Colors.green.withOpacity(0.5), width: 2),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.green.withOpacity(0.2),
                          blurRadius: 20,
                          spreadRadius: 5,
                        ),
                      ],
                    ),
                    child: const Icon(
                      Icons.check_rounded,
                      color: Colors.greenAccent,
                      size: 48,
                    ).animate().scale(duration: 400.ms, curve: Curves.elasticOut),
                  ),
                  const SizedBox(height: 24),

                  Text(
                    languageProvider.getText('congratulations'),
                    style: GoogleFonts.poppins(
                      fontSize: 24,
                      fontWeight: FontWeight.bold,
                      color: Colors.white,
                    ),
                  ).animate().fadeIn(delay: 200.ms).slideY(begin: 0.2, end: 0),
                  
                  const SizedBox(height: 12),
                  
                  Text(
                    languageProvider.getText('earned_coins'),
                    textAlign: TextAlign.center,
                    style: GoogleFonts.poppins(
                      fontSize: 16,
                      color: Colors.white70,
                    ),
                  ).animate().fadeIn(delay: 300.ms),
                  
                  const SizedBox(height: 32),
                  
                  SizedBox(
                    width: double.infinity,
                    height: 50,
                    child: ElevatedButton(
                      onPressed: () => Navigator.pop(context),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.green,
                        foregroundColor: Colors.white,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(16),
                        ),
                        elevation: 0,
                      ),
                      child: Text(
                        languageProvider.getText('awesome').toUpperCase(),
                        style: GoogleFonts.poppins(
                          fontWeight: FontWeight.bold,
                          letterSpacing: 1,
                        ),
                      ),
                    ),
                  ).animate().scale(delay: 400.ms, duration: 300.ms, curve: Curves.easeOutBack),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  bool _isDownloading = false;

  Future<void> _downloadWallpaper() async {
    if (_isDownloading) return;

    final languageProvider = Provider.of<LanguageProvider>(context, listen: false);

    setState(() {
      _isDownloading = true;
    });

    ProfessionalToast.showLoading(context, message: languageProvider.getText('downloading_started'));

    try {
      // 1. Track Download on Server
      await Provider.of<AppProvider>(context, listen: false).downloadWallpaper(widget.wallpaper.id);

      // 2. Request Permissions
      // For Android 13+, storage permission might not be needed for just saving images via media store,
      // but we request photos/storage for compatibility.
      if (await Permission.storage.request().isDenied) {
        await Permission.photos.request();
      }

      // 3. Download using Cache Manager (efficient & handles temp file)
      var file = await DefaultCacheManager().getSingleFile(widget.wallpaper.url);
      
      // 4. Save to Gallery
      // ImageGallerySaverPlus handles platform specific saving
      final result = await ImageGallerySaverPlus.saveFile(file.path, name: "wallpaper_${widget.wallpaper.id}");

      if (mounted) {
        // Result can be a Map or boolean depending on platform/version
        bool success = false;
        if (result is Map) {
          success = result['isSuccess'] == true;
        } else if (result is bool) {
          success = result;
        } else {
          success = result != null;
        }

        if (success) {
          ProfessionalToast.showSuccess(context, message: languageProvider.getText('saved_success'));
        } else {
          ProfessionalToast.showError(context, message: languageProvider.getText('save_failed'));
        }
      }
    } catch (e) {
      print("Download Error: $e");
      if (mounted) {
        ProfessionalToast.showError(context, message: "${languageProvider.getText('error_prefix')}$e");
      }
    } finally {
      if (mounted) {
        setState(() {
          _isDownloading = false;
        });
      }
    }
  }

  Future<void> _applyWallpaper(int location) async {
    final languageProvider = Provider.of<LanguageProvider>(context, listen: false);
    Navigator.pop(context); // Close dialog

    // Show Ad before apply
    final adProvider = Provider.of<AdProvider>(context, listen: false);
    if (adProvider.adsEnabled) {
       final fallbacks = adProvider.adPriorities.isNotEmpty ? adProvider.adPriorities : ['admob', 'facebook', 'unity'];
       await AdManager.showAdWithFallback(context, fallbacks, () {});
    }

    ProfessionalToast.showLoading(context, message: languageProvider.getText('applying_wallpaper'));

    try {
      // Download file first
      var file = await DefaultCacheManager().getSingleFile(widget.wallpaper.url);
      
      bool result = false;
      int wallpaperLocation = AsyncWallpaper.HOME_SCREEN;
      
      if (location == 1) wallpaperLocation = AsyncWallpaper.HOME_SCREEN;
      else if (location == 2) wallpaperLocation = AsyncWallpaper.LOCK_SCREEN;
      else if (location == 3) wallpaperLocation = AsyncWallpaper.BOTH_SCREENS;

      // Use async_wallpaper to set from file
      try {
        if (wallpaperLocation == AsyncWallpaper.BOTH_SCREENS) {
           // Fallback for Both Screens if plugin has issues with specific 'both' method on some devices
           // Try official method first
           try {
             result = await AsyncWallpaper.setWallpaperFromFile(
               filePath: file.path,
               wallpaperLocation: AsyncWallpaper.BOTH_SCREENS,
               goToHome: false,
             );
           } catch (e) {
             // Fallback: Set Home then Lock
             bool homeRes = await AsyncWallpaper.setWallpaperFromFile(
               filePath: file.path,
               wallpaperLocation: AsyncWallpaper.HOME_SCREEN,
               goToHome: false,
             );
             bool lockRes = await AsyncWallpaper.setWallpaperFromFile(
               filePath: file.path,
               wallpaperLocation: AsyncWallpaper.LOCK_SCREEN,
               goToHome: false,
             );
             result = homeRes && lockRes;
           }
        } else {
           result = await AsyncWallpaper.setWallpaperFromFile(
            filePath: file.path,
            wallpaperLocation: wallpaperLocation,
            goToHome: false,
          );
        }
      } catch (e) {
         // Final fallback if something really weird happens
         print("Primary Apply Failed: $e");
         result = false;
         throw e; 
      }

      if (mounted) {
        if (result) {
          ProfessionalToast.showSuccess(context, message: languageProvider.getText('wallpaper_applied'));
        } else {
          ProfessionalToast.showError(context, message: languageProvider.getText('apply_failed'));
        }
      }
    } on MissingPluginException {
      print("AsyncWallpaper Plugin Missing");
      if (mounted) {
         ProfessionalToast.showError(context, message: "Wallpaper Plugin Error. Please restart the app.");
      }
    } catch (e) {
      print("Apply Error: $e");
      if (mounted) {
         ProfessionalToast.showError(context, message: "${languageProvider.getText('apply_failed')} $e");
      }
    }
  }

  void _showApplyWallpaperDialog() {
    final lang = Provider.of<LanguageProvider>(context, listen: false);
    showDialog(
      context: context,
      barrierColor: Colors.black.withOpacity(0.8),
      builder: (context) => Dialog(
        backgroundColor: Colors.transparent,
        insetPadding: const EdgeInsets.symmetric(horizontal: 24),
        child: Container(
          width: double.infinity,
          padding: const EdgeInsets.all(24),
          decoration: BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [
                const Color(0xFF2A2D3E),
                const Color(0xFF1F1B2E),
              ],
            ),
            borderRadius: BorderRadius.circular(28),
            border: Border.all(color: Colors.white.withOpacity(0.1), width: 1),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.5),
                blurRadius: 30,
                spreadRadius: 10,
              ),
            ],
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                lang.getText('apply_wallpaper_to').toUpperCase(),
                style: GoogleFonts.poppins(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  color: Colors.white,
                  letterSpacing: 1.2,
                ),
              ),
              const SizedBox(height: 30),
              _buildApplyOption(Icons.home_rounded, lang.getText('home_screen'), () => _applyWallpaper(1)),
              const SizedBox(height: 15),
              _buildApplyOption(Icons.lock_rounded, lang.getText('lock_screen'), () => _applyWallpaper(2)),
              const SizedBox(height: 15),
              _buildApplyOption(Icons.mobile_screen_share_rounded, lang.getText('both_screens'), () => _applyWallpaper(3)),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildApplyOption(IconData icon, String text, VoidCallback onTap) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(16),
        child: Container(
          width: double.infinity,
          padding: const EdgeInsets.symmetric(vertical: 16, horizontal: 20),
          decoration: BoxDecoration(
            color: Colors.white.withOpacity(0.05),
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: Colors.white.withOpacity(0.1)),
          ),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.1),
                  shape: BoxShape.circle,
                ),
                child: Icon(icon, color: Colors.white, size: 20),
              ),
              const SizedBox(width: 16),
              Text(
                text.toUpperCase(),
                style: GoogleFonts.poppins(
                  color: Colors.white,
                  fontSize: 14,
                  fontWeight: FontWeight.w600,
                  letterSpacing: 1.0,
                ),
              ),
              const Spacer(),
              Icon(Icons.arrow_forward_ios_rounded, color: Colors.white.withOpacity(0.3), size: 16),
            ],
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final languageProvider = Provider.of<LanguageProvider>(context);
    return Scaffold(
      backgroundColor: AppTheme.darkBackgroundColor,
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios, color: Colors.white),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: Stack(
        children: [
          // Full Screen Image
          Positioned.fill(
            child: Hero(
              tag: widget.heroTag ?? widget.wallpaper.url,
              child: CachedNetworkImage(
                imageUrl: widget.wallpaper.url,
                fit: BoxFit.cover,
                memCacheHeight: MediaQuery.of(context).size.height.toInt() * 2, // High quality but bounded
                placeholder: (context, url) => const Center(child: CircularProgressIndicator()),
                errorWidget: (context, url, error) => const Icon(Icons.error),
              ),
            ),
          ),
          
          // Banner Ad
          Positioned(
            bottom: 0,
            left: 0,
            right: 0,
            child: SafeArea(
              top: false,
              child: SizedBox(
                height: 50,
                child: UniversalBannerAd(screen: 'details'),
              ),
            ),
          ),

          // Bottom Actions
          Positioned(
            bottom: 100,
            left: 20,
            right: 20,
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              children: [
                _buildActionButton(Icons.download, _isDownloading ? languageProvider.getText('wait') : languageProvider.getText('download'), () {
                  _downloadWallpaper();
                }),
                _buildActionButton(Icons.wallpaper, languageProvider.getText('apply'), () {
                  _showApplyWallpaperDialog();
                }),
              ],
            ),
          ),

          // Reward Timer / Coin Button
          Positioned(
            top: 100,
            right: 20,
            child: AnimatedBuilder(
              animation: _controller,
              builder: (context, child) {
                return GestureDetector(
                  onTap: _isRewardReady ? _showCoinDialog : null,
                  child: Container(
                    width: 60,
                    height: 60,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: _isRewardReady ? Colors.amber : Colors.black54,
                      boxShadow: _isRewardReady
                          ? [BoxShadow(color: Colors.amber.withOpacity(0.6), blurRadius: 15, spreadRadius: 2)]
                          : [],
                      border: Border.all(color: Colors.white24, width: 1),
                    ),
                    child: Stack(
                      alignment: Alignment.center,
                      children: [
                        if (!_isRewardReady)
                          SizedBox(
                            width: 52,
                            height: 52,
                            child: CircularProgressIndicator(
                              value: _controller.value,
                              strokeWidth: 4,
                              valueColor: const AlwaysStoppedAnimation<Color>(Colors.amber),
                              backgroundColor: Colors.white10,
                            ),
                          ),
                        Icon(
                          _isRewardReady ? Icons.monetization_on : Icons.hourglass_bottom,
                          color: Colors.white,
                          size: 28,
                        ),
                      ],
                    ),
                  ).animate(target: _isRewardReady ? 1 : 0).shake(duration: 500.ms),
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildActionButton(IconData icon, String label, VoidCallback onTap) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(25),
        child: Container(
          width: 120,
          height: 50,
          alignment: Alignment.center,
          decoration: BoxDecoration(
            color: Colors.black.withOpacity(0.6),
            borderRadius: BorderRadius.circular(25),
            border: Border.all(color: Colors.white.withOpacity(0.2)),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.3),
                blurRadius: 10,
                offset: const Offset(0, 4),
              ),
            ],
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(icon, color: Colors.white, size: 22),
              const SizedBox(width: 8),
              Text(
                label,
                style: GoogleFonts.poppins(
                  color: Colors.white,
                  fontWeight: FontWeight.w500,
                  fontSize: 14,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
