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
import '../providers/language_provider.dart';
import '../services/google_ad_service.dart';
import '../services/api_service.dart';
import 'auth/login_screen.dart';
import '../utils/app_theme.dart';

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
    
    // Define validation result
    bool isTokenValid = false;

    // Validation Task
    Future<void> validateTokenTask() async {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');
      
      if (token != null && token.isNotEmpty) {
        try {
          // Verify token with server
          final result = await ApiService().getUserDetails().timeout(const Duration(seconds: 5));
          if (result['success'] == true) {
            isTokenValid = true;
          } else {
            // Invalid token, clear it
            await prefs.remove('auth_token');
            await prefs.remove('user_data');
            isTokenValid = false;
          }
        } catch (e) {
          print("Splash validation error: $e");
          // If error (e.g. timeout), assume invalid to be safe and force login
          isTokenValid = false;
        }
      } else {
        isTokenValid = false;
      }
    }

    // Wait for minimum splash duration, data loading, and token validation
    await Future.wait([
      Future.delayed(const Duration(seconds: 3)), 
      _initializeData(),
      validateTokenTask(),
    ]);

    if (!mounted) return;

    if (isTokenValid) {
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (context) => const MainScreen()),
      );
    } else {
      // If not logged in or invalid token, force Login Page as requested
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (context) => const LoginScreen()),
      );
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
    return Consumer<LanguageProvider>(
      builder: (context, langProvider, child) {
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
              decoration: AppTheme.backgroundDecoration,
              child: SafeArea(
                child: Center(
                  child: SingleChildScrollView(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Container(
                          height: 150,
                          width: 150,
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            boxShadow: [
                              // Neon Glow Effect
                              BoxShadow(
                                color: Colors.cyanAccent.withOpacity(0.6),
                                blurRadius: 50,
                                spreadRadius: 10,
                              ),
                              BoxShadow(
                                color: Colors.purpleAccent.withOpacity(0.4),
                                blurRadius: 30,
                                spreadRadius: 5,
                              ),
                            ],
                          ),
                          child: ClipOval(
                            child: Image.asset(
                              'assets/app_icon.png',
                              fit: BoxFit.cover,
                            ),
                          ),
                        ).animate()
                         .scale(duration: 800.ms, curve: Curves.elasticOut)
                         .then()
                         .shimmer(duration: 1200.ms, color: Colors.white.withOpacity(0.5)),
                        
                        const SizedBox(height: 30),
                        
                        Text(
                          langProvider.getText('app_name'),
                          style: GoogleFonts.poppins(
                            fontSize: 40,
                            fontWeight: FontWeight.bold,
                            color: Colors.white,
                            letterSpacing: 2,
                            shadows: [
                              Shadow(
                                color: Colors.cyanAccent.withOpacity(0.8),
                                blurRadius: 20,
                                offset: const Offset(0, 0),
                              ),
                            ],
                          ),
                        ).animate().fadeIn(delay: 500.ms).slideY(begin: 0.5, end: 0),
                        
                        const SizedBox(height: 10),
                        
                        Text(
                          langProvider.getText('app_tagline'),
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
      },
    );
  }
}
