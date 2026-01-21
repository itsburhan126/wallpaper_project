import 'package:flutter/material.dart';

class AppTheme {
  // Private constructor to prevent instantiation
  AppTheme._();

  // Core Colors
  static const Color darkBackgroundColor = Color(0xFF120C24);
  static const Color primaryColor = Color(0xFF2E1C59);
  
  // Gradients
  static const LinearGradient backgroundGradient = LinearGradient(
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
    colors: [
      Color(0xFF2E1C59),
      Color(0xFF120C24),
    ],
  );

  static const LinearGradient cardGradient = LinearGradient(
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
    colors: [
      Color(0xFF2A1B4E),
      Color(0xFF1A1230),
    ],
  );

  // Common Decorations
  static BoxDecoration get backgroundDecoration => const BoxDecoration(
    gradient: backgroundGradient,
  );
}
