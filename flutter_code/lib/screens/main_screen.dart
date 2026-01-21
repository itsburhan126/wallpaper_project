import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:glassmorphism/glassmorphism.dart';
import 'package:flutter_animate/flutter_animate.dart';
import '../utils/constants.dart';
import 'home_screen.dart';
import 'shorts_screen.dart';
import 'category_screen.dart';
import 'task_screen.dart';
import 'profile_screen.dart';
import '../utils/app_theme.dart';

class MainScreen extends StatefulWidget {
  const MainScreen({super.key});

  @override
  State<MainScreen> createState() => _MainScreenState();
}

class _MainScreenState extends State<MainScreen> {
  int _currentIndex = 0;
  
  final List<Widget> _screens = [
    // Navigation Screens
    const HomeScreen(),
    const ShortsScreen(),
    const CategoryScreen(),
    const TaskScreen(),
    const ProfileScreen(),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.darkBackgroundColor, // Match the dark theme
      extendBody: true, // For glassmorphism effect on bottom nav
      body: IndexedStack( // Use IndexedStack to preserve state
        index: _currentIndex,
        children: _screens,
      ),
      bottomNavigationBar: GlassmorphicContainer(
        width: double.infinity,
        height: 100, // Increased height to prevent overflow
        borderRadius: 0,
        linearGradient: LinearGradient(
          begin: Alignment.topCenter,
          end: Alignment.bottomCenter,
          colors: [
            AppTheme.darkBackgroundColor.withOpacity(0.9),
            AppTheme.darkBackgroundColor.withOpacity(0.7),
          ],
        ),
        border: 0,
        blur: 15,
        borderGradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            Colors.white.withOpacity(0.1),
            Colors.white.withOpacity(0.05),
          ],
        ),
        child: Theme(
          data: Theme.of(context).copyWith(
            splashColor: Colors.transparent,
            highlightColor: Colors.transparent,
          ),
          child: BottomNavigationBar(
            backgroundColor: Colors.transparent,
            elevation: 0,
            currentIndex: _currentIndex,
            selectedItemColor: AppColors.accent,
            unselectedItemColor: Colors.white54,
            showUnselectedLabels: true,
            type: BottomNavigationBarType.fixed,
            selectedLabelStyle: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 12),
            unselectedLabelStyle: GoogleFonts.poppins(fontWeight: FontWeight.w400, fontSize: 12),
            onTap: (index) {
              setState(() {
                _currentIndex = index;
              });
            },
            items: [
              BottomNavigationBarItem(
                icon: const Icon(FontAwesomeIcons.house, size: 20)
                    .animate(target: _currentIndex == 0 ? 1 : 0)
                    .scale(begin: const Offset(1, 1), end: const Offset(1.2, 1.2), duration: 200.ms)
                    .tint(color: AppColors.accent),
                label: 'Home',
              ),
              BottomNavigationBarItem(
                icon: const Icon(FontAwesomeIcons.clapperboard, size: 20)
                    .animate(target: _currentIndex == 1 ? 1 : 0)
                    .scale(begin: const Offset(1, 1), end: const Offset(1.2, 1.2), duration: 200.ms)
                    .tint(color: AppColors.accent),
                label: 'Shorts',
              ),
              BottomNavigationBarItem(
                icon: const Icon(FontAwesomeIcons.layerGroup, size: 20)
                    .animate(target: _currentIndex == 2 ? 1 : 0)
                    .scale(begin: const Offset(1, 1), end: const Offset(1.2, 1.2), duration: 200.ms)
                    .tint(color: AppColors.accent),
                label: 'Category',
              ),
              BottomNavigationBarItem(
                icon: const Icon(FontAwesomeIcons.listCheck, size: 20)
                    .animate(target: _currentIndex == 3 ? 1 : 0)
                    .scale(begin: const Offset(1, 1), end: const Offset(1.2, 1.2), duration: 200.ms)
                    .tint(color: AppColors.accent),
                label: 'Task',
              ),
              BottomNavigationBarItem(
                icon: const Icon(FontAwesomeIcons.user, size: 20)
                    .animate(target: _currentIndex == 4 ? 1 : 0)
                    .scale(begin: const Offset(1, 1), end: const Offset(1.2, 1.2), duration: 200.ms)
                    .tint(color: AppColors.accent),
                label: 'Profile',
              ),
            ],
          ),
        ),
      ).animate().fadeIn(delay: 1000.ms).slideY(begin: 1, end: 0),
    );
  }
}
