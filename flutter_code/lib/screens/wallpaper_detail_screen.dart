import 'dart:typed_data';
import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:provider/provider.dart';
import 'package:glassmorphism/glassmorphism.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:dio/dio.dart';
import 'package:image_gallery_saver_plus/image_gallery_saver_plus.dart';
import 'package:permission_handler/permission_handler.dart';
import 'package:flutter/services.dart'; // Add this for MissingPluginException
import 'package:async_wallpaper/async_wallpaper.dart';
import 'package:flutter_cache_manager/flutter_cache_manager.dart';
import '../models/wallpaper_model.dart';
import '../providers/app_provider.dart';
import '../providers/language_provider.dart';
import '../utils/constants.dart';
import '../utils/app_theme.dart';
import '../services/ad_manager_service.dart';
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
            colors: [
              Colors.white.withOpacity(0.1),
              Colors.white.withOpacity(0.05),
            ],
          ),
          borderGradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [
              Colors.white.withOpacity(0.5),
              Colors.white.withOpacity(0.1),
            ],
          ),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.monetization_on, color: Colors.amber, size: 50),
              const SizedBox(height: 20),
              Text(
                languageProvider.getText('earn_coins'),
                style: AppTextStyles.header,
              ),
              const SizedBox(height: 10),
              Text(
                languageProvider.getText('watch_ad_text'),
                style: AppTextStyles.body,
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 20),
              ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppColors.accent,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(30)),
                ),
                onPressed: () {
                  Navigator.pop(context); // Close dialog
                  _watchAd();
                },
                child: Text(languageProvider.getText('watch_ad_btn'), style: const TextStyle(color: Colors.white)),
              ),
            ],
          ),
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

    // Fallback order - forcing AdMob as per general preference for now
    final fallbacks = ['admob'];
    
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
              colors: [Colors.green.withOpacity(0.2), Colors.green.withOpacity(0.1)]),
          borderGradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [Colors.green.withOpacity(0.5), Colors.green.withOpacity(0.1)]),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.check_circle, color: Colors.greenAccent, size: 60)
                  .animate()
                  .scale(duration: 500.ms, curve: Curves.elasticOut),
              const SizedBox(height: 20),
              Text(languageProvider.getText('congratulations'), style: AppTextStyles.header),
              const SizedBox(height: 10),
              Text(languageProvider.getText('earned_coins'), style: AppTextStyles.body),
              const SizedBox(height: 20),
              ElevatedButton(
                onPressed: () => Navigator.pop(context),
                child: Text(languageProvider.getText('awesome')),
              )
            ],
          ),
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
      builder: (context) => Dialog(
        backgroundColor: Colors.transparent,
        child: GlassmorphicContainer(
          width: 300,
          height: 320,
          borderRadius: 20,
          blur: 20,
          alignment: Alignment.center,
          border: 2,
          linearGradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [
              Colors.white.withOpacity(0.1),
              Colors.white.withOpacity(0.05),
            ],
          ),
          borderGradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [
              Colors.white.withOpacity(0.5),
              Colors.white.withOpacity(0.1),
            ],
          ),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Text(lang.getText('apply_wallpaper_to').toUpperCase(), style: AppTextStyles.header.copyWith(fontSize: 18, letterSpacing: 1.2)),
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
    return InkWell(
      onTap: onTap,
      child: Container(
        width: 240,
        padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 20),
        decoration: BoxDecoration(
          color: Colors.white.withOpacity(0.1),
          borderRadius: BorderRadius.circular(30),
          border: Border.all(color: Colors.white.withOpacity(0.2)),
        ),
        child: Row(
          children: [
            Icon(icon, color: Colors.white, size: 20),
            const SizedBox(width: 15),
            Text(text.toUpperCase(), style: const TextStyle(color: Colors.white, fontSize: 14, fontWeight: FontWeight.bold, letterSpacing: 1.0)),
          ],
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
                placeholder: (context, url) => const Center(child: CircularProgressIndicator()),
                errorWidget: (context, url, error) => const Icon(Icons.error),
              ),
            ),
          ),
          
          // Bottom Actions
          Positioned(
            bottom: 40,
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
    return GlassmorphicContainer(
      width: 120,
      height: 50,
      borderRadius: 25,
      blur: 10,
      alignment: Alignment.center,
      border: 1,
      linearGradient: LinearGradient(
        colors: [Colors.black.withOpacity(0.5), Colors.black.withOpacity(0.3)],
      ),
      borderGradient: LinearGradient(
        colors: [Colors.white.withOpacity(0.2), Colors.white.withOpacity(0.1)],
      ),
      child: InkWell(
        onTap: onTap,
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon, color: Colors.white),
            const SizedBox(width: 8),
            Text(label, style: const TextStyle(color: Colors.white)),
          ],
        ),
      ),
    );
  }
}
