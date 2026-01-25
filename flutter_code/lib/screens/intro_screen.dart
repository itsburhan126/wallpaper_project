import 'package:flutter/material.dart';
import 'package:introduction_screen/introduction_screen.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../utils/constants.dart';
import 'auth/login_screen.dart';
import '../utils/app_theme.dart';
import '../providers/language_provider.dart';

class IntroScreen extends StatelessWidget {
  const IntroScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Consumer<LanguageProvider>(
      builder: (context, langProvider, child) {
        return Container(
          decoration: AppTheme.backgroundDecoration,
          child: Scaffold(
            backgroundColor: Colors.transparent,
            body: IntroductionScreen(
              globalBackgroundColor: Colors.transparent,
              pages: [
                PageViewModel(
                  title: langProvider.getText('intro_title_1'),
                  body: langProvider.getText('intro_desc_1'),
                  image: _buildImage(context, Icons.wallpaper, Colors.cyanAccent),
                  decoration: _pageDecoration(),
                ),
                PageViewModel(
                  title: langProvider.getText('intro_title_2'),
                  body: langProvider.getText('intro_desc_2'),
                  image: _buildImage(context, Icons.gamepad, Colors.purpleAccent),
                  decoration: _pageDecoration(),
                ),
                PageViewModel(
                  title: langProvider.getText('intro_title_3'),
                  body: langProvider.getText('intro_desc_3'),
                  image: _buildImage(context, Icons.card_giftcard, Colors.amberAccent),
                  decoration: _pageDecoration(),
                ),
              ],
              onDone: () => _onIntroEnd(context),
              onSkip: () => _onIntroEnd(context),
              showSkipButton: true,
              skipOrBackFlex: 0,
              nextFlex: 0,
              showBackButton: false,
              back: const Icon(Icons.arrow_back, color: Colors.white),
              skip: Text(langProvider.getText('skip'), style: const TextStyle(fontWeight: FontWeight.w600, color: Colors.white70)),
              next: const Icon(Icons.arrow_forward, color: Colors.white),
              done: Container(
                padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
                decoration: BoxDecoration(
                  color: AppColors.accent,
                  borderRadius: BorderRadius.circular(20),
                  boxShadow: [
                    BoxShadow(
                      color: AppColors.accent.withOpacity(0.4),
                      blurRadius: 10,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: Text(langProvider.getText('get_started'), style: const TextStyle(fontWeight: FontWeight.w600, color: Colors.white)),
              ),
              curve: Curves.fastLinearToSlowEaseIn,
              controlsMargin: const EdgeInsets.fromLTRB(16, 16, 16, 32),
              controlsPadding: const EdgeInsets.fromLTRB(8.0, 4.0, 8.0, 4.0),
              safeAreaList: [true, true, true, true],
              dotsDecorator: DotsDecorator(
                size: const Size(10.0, 10.0),
                color: Colors.white24,
                activeSize: const Size(22.0, 10.0),
                activeShape: const RoundedRectangleBorder(
                  borderRadius: BorderRadius.all(Radius.circular(25.0)),
                ),
                activeColor: AppColors.accent,
              ),
            ),
          ),
        );
      }
    );
  }

  void _onIntroEnd(BuildContext context) {
    Navigator.of(context).pushReplacement(
      MaterialPageRoute(builder: (_) => const LoginScreen()),
    );
  }

  Widget _buildImage(BuildContext context, IconData icon, Color color) {
    double width = MediaQuery.of(context).size.width * 0.6;
    double height = MediaQuery.of(context).size.height * 0.35; // Constrain by height too
    
    double size = width < height ? width : height;
    if (size > 280) size = 280;
    
    return Center(
      child: Container(
        width: size,
        height: size,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          gradient: RadialGradient(
            colors: [
              color.withOpacity(0.2),
              Colors.transparent,
            ],
            stops: const [0.5, 1.0],
          ),
        ),
        child: Container(
          decoration: BoxDecoration(
            shape: BoxShape.circle,
            color: Colors.white.withOpacity(0.05),
            border: Border.all(color: color.withOpacity(0.3), width: 2),
            boxShadow: [
              BoxShadow(
                color: color.withOpacity(0.1),
                blurRadius: 20,
                spreadRadius: 5,
              ),
            ],
          ),
          child: Icon(
            icon,
            size: 100,
            color: color,
          ),
        ),
      ),
    );
  }

  PageDecoration _pageDecoration() {
    return PageDecoration(
      titleTextStyle: GoogleFonts.poppins(
        fontSize: 32.0, 
        fontWeight: FontWeight.w700, 
        color: Colors.white,
        shadows: [
          Shadow(
            color: Colors.black.withOpacity(0.5),
            blurRadius: 10,
            offset: const Offset(0, 5),
          ),
        ],
      ),
      bodyTextStyle: GoogleFonts.poppins(
        fontSize: 18.0, 
        color: Colors.white70,
        height: 1.5,
      ),
      bodyPadding: const EdgeInsets.fromLTRB(16.0, 0.0, 16.0, 16.0),
      pageColor: Colors.transparent,
      imagePadding: const EdgeInsets.only(bottom: 24),
      contentMargin: const EdgeInsets.only(bottom: 16), // Added to prevent bottom overlap
      safeArea: 80, // Add safe area padding at bottom
    );
  }
}
