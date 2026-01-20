import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class AppColors {
  static const Color primary = Color(0xFF6C63FF);
  static const Color secondary = Color(0xFF2A2D3E);
  static const Color background = Color(0xFF1F1D2B);
  static const Color cardColor = Color(0xFF252836);
  static const Color accent = Color(0xFFFF7551);
  static const Color textPrimary = Colors.white;
  static const Color textSecondary = Colors.grey;
}

class AppTextStyles {
  static TextStyle get header => GoogleFonts.poppins(
    fontSize: 24,
    fontWeight: FontWeight.bold,
    color: AppColors.textPrimary,
  );
  
  static TextStyle get subHeader => GoogleFonts.poppins(
    fontSize: 18,
    fontWeight: FontWeight.w600,
    color: AppColors.textPrimary,
  );

  static TextStyle get body => GoogleFonts.poppins(
    fontSize: 14,
    color: AppColors.textSecondary,
  );
  
  static TextStyle get button => GoogleFonts.poppins(
    fontSize: 16,
    fontWeight: FontWeight.bold,
    color: Colors.white,
  );
}
