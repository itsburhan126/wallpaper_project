import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class AnimatedCoinBalance extends StatelessWidget {
  final int balance;
  final TextStyle? style;

  const AnimatedCoinBalance({
    super.key,
    required this.balance,
    this.style,
  });

  @override
  Widget build(BuildContext context) {
    return TweenAnimationBuilder<int>(
      tween: IntTween(begin: 0, end: balance),
      duration: const Duration(milliseconds: 1500), 
      curve: Curves.easeOutExpo,
      builder: (context, value, child) {
        return Text(
          "$value",
          style: style ?? GoogleFonts.poppins(
            fontSize: 14,
            fontWeight: FontWeight.bold,
            color: Colors.amber,
          ),
        );
      },
    );
  }
}
