import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:glassmorphism/glassmorphism.dart';
import '../utils/constants.dart';

class CategoryScreen extends StatelessWidget {
  const CategoryScreen({super.key});

  final List<String> categories = const [
    "Abstract", "Nature", "Games", "Anime", "Cars", "Space", "Dark", "Minimal", "Cyberpunk", "Neon"
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.black,
      body: Container(
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [
              const Color(0xFF1A237E).withOpacity(0.2),
              const Color(0xFF000000),
            ],
          ),
        ),
        child: SafeArea(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Padding(
                padding: const EdgeInsets.all(20.0),
                child: Text(
                  "Categories",
                  style: GoogleFonts.poppins(
                    fontSize: 28,
                    fontWeight: FontWeight.bold,
                    color: Colors.white,
                  ),
                ).animate().fadeIn().slideX(),
              ),
              Expanded(
                child: GridView.builder(
                  padding: const EdgeInsets.symmetric(horizontal: 20),
                  gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                    crossAxisCount: 2,
                    crossAxisSpacing: 15,
                    mainAxisSpacing: 15,
                    childAspectRatio: 1.5,
                  ),
                  itemCount: categories.length,
                  itemBuilder: (context, index) {
                    return GlassmorphicContainer(
                      width: double.infinity,
                      height: double.infinity,
                      borderRadius: 16,
                      blur: 10,
                      alignment: Alignment.center,
                      border: 1,
                      linearGradient: LinearGradient(
                        colors: [Colors.white.withOpacity(0.1), Colors.white.withOpacity(0.05)],
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                      ),
                      borderGradient: LinearGradient(
                        colors: [Colors.white.withOpacity(0.2), Colors.white.withOpacity(0.1)],
                      ),
                      child: Stack(
                        fit: StackFit.expand,
                        children: [
                          // Background Image Placeholder (Gradient for now)
                          Container(
                            decoration: BoxDecoration(
                              gradient: LinearGradient(
                                colors: [
                                  Colors.primaries[index % Colors.primaries.length].withOpacity(0.3),
                                  Colors.transparent,
                                ],
                                begin: Alignment.bottomLeft,
                                end: Alignment.topRight,
                              ),
                            ),
                          ),
                          Center(
                            child: Text(
                              categories[index],
                              style: GoogleFonts.poppins(
                                color: Colors.white,
                                fontSize: 18,
                                fontWeight: FontWeight.w600,
                                letterSpacing: 1,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ).animate().scale(delay: (100 * index).ms, duration: 400.ms);
                  },
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
