import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../utils/constants.dart';
import 'intro_screen.dart';
import 'main_screen.dart';
import '../providers/ad_provider.dart';
import '../services/google_ad_service.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  @override
  void initState() {
    super.initState();
    _checkAuthAndNavigate();
  }

  _checkAuthAndNavigate() async {
    // Start fetching ad settings immediately
    final adProvider = Provider.of<AdProvider>(context, listen: false);
    // Wait for settings to be loaded if they are not yet
    if (adProvider.isLoading) {
       // Just a small delay to allow provider to start fetching if it hasn't
       await Future.delayed(const Duration(milliseconds: 100));
       // We can't easily await the provider's internal future unless we expose it, 
       // but fetchAdSettings is called in constructor.
       // We can wait a bit or just proceed. 
       // Better approach: ensure we have settings before preloading.
    }
    
    // Preload Ads (Fire and forget, but try to ensure settings are loaded)
    // We will wait for splash duration anyway.
    
    final startTime = DateTime.now();
    
    // Wait for minimum splash duration and data loading
    await Future.wait([
      Future.delayed(const Duration(seconds: 3)), // Increased to 3s to give time for ad load
      _initializeData(),
    ]);

    if (!mounted) return;

    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('auth_token');
    
    if (mounted) {
      if (token != null && token.isNotEmpty) {
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (context) => const MainScreen()),
        );
      } else {
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (context) => const IntroScreen()),
        );
      }
    }
  }

  Future<void> _initializeData() async {
    // Ensure Ad Settings are loaded
    final adProvider = Provider.of<AdProvider>(context, listen: false);
    
    // Simple polling to wait for ad settings (since we don't have a future to await on provider)
    int retries = 0;
    while (adProvider.isLoading && retries < 20) {
      await Future.delayed(const Duration(milliseconds: 100));
      retries++;
    }

    if (mounted) {
       // Trigger Ad Preload
       print("🚀 Splash: Preloading Ads...");
       GoogleAdService().loadRewardedAd(context);
       GoogleAdService().loadInterstitialAd(context);
    }
  }

  @override
  Widget build(BuildContext context) {
    return AnnotatedRegion<SystemUiOverlayStyle>(
      value: const SystemUiOverlayStyle(
        statusBarColor: Colors.transparent,
        statusBarIconBrightness: Brightness.light,
        systemNavigationBarColor: Colors.transparent,
        systemNavigationBarIconBrightness: Brightness.light,
      ),
      child: Scaffold(
        extendBody: true,
        backgroundColor: Colors.transparent,
        body: Container(
          width: double.infinity,
          height: double.infinity,
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [
                Color(0xFF1A237E), // Deep Blue
                Color(0xFF000000), // Black
              ],
            ),
          ),
          child: SafeArea(
            child: Center(
              child: SingleChildScrollView(
                child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
            Container(
              height: 120,
              width: 120,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                gradient: LinearGradient(
                  colors: [AppColors.primary, AppColors.accent],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                boxShadow: [
                  BoxShadow(
                    color: AppColors.primary.withOpacity(0.5),
                    blurRadius: 30,
                    spreadRadius: 10,
                  )
                ],
              ),
              child: const Icon(
                Icons.wallpaper,
                size: 60,
                color: Colors.white,
              ),
            ).animate()
             .scale(duration: 800.ms, curve: Curves.elasticOut)
             .then()
             .shimmer(duration: 1200.ms, color: Colors.white.withOpacity(0.5)),
            
            const SizedBox(height: 30),
            
            Text(
              "ProWall",
              style: GoogleFonts.poppins(
                fontSize: 40,
                fontWeight: FontWeight.bold,
                color: Colors.white,
                letterSpacing: 2,
              ),
            ).animate().fadeIn(delay: 500.ms).slideY(begin: 0.5, end: 0),
            
            const SizedBox(height: 10),
            
            Text(
              "Premium Wallpapers & Games",
              style: GoogleFonts.poppins(
                fontSize: 16,
                color: Colors.grey,
                letterSpacing: 1,
              ),
            ).animate().fadeIn(delay: 800.ms),
          ],
        ),
      ),
    ),
      ),
      ),
    ),
    );
  }
}
