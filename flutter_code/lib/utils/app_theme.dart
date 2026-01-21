import 'package:flutter/material.dart';

class AppTheme {
  // Private constructor to prevent instantiation
  AppTheme._();

  // Core Colors
  static const Color darkBackgroundColor = Color(0xFF000000);
  static const Color primaryColor = Color(0xFF1A1A1A);
  
  // Gradients
  static const LinearGradient backgroundGradient = LinearGradient(
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
    colors: [
      Color(0xFF141414), // Very dark grey
      Color(0xFF000000), // Pure black
    ],
  );

  static const LinearGradient cardGradient = LinearGradient(
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
    colors: [
      Color(0xFF1C1C1E),
      Color(0xFF0A0A0A),
    ],
  );

  // Common Decorations
  static BoxDecoration get backgroundDecoration => const BoxDecoration(
    gradient: backgroundGradient,
  );
}
