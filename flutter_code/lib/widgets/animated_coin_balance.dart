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
      tween: IntTween(begin: balance, end: balance),
      duration: const Duration(seconds: 1), // 1 second to count up
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
