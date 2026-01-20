import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:flutter_animate/flutter_animate.dart';

class GameWarningDialog extends StatelessWidget {
  final int playedSeconds;
  final int requiredSeconds;
  final int rewardAmount;
  final VoidCallback onClose;

  const GameWarningDialog({
    super.key,
    required this.playedSeconds,
    required this.requiredSeconds,
    required this.rewardAmount,
    required this.onClose,
  });

  String _formatDuration(int seconds) {
    if (seconds < 60) {
      return "$seconds Seconds";
    } else {
      final minutes = (seconds / 60).floor();
      final remainingSeconds = seconds % 60;
      return "$minutes:${remainingSeconds.toString().padLeft(2, '0')} Minutes";
    }
  }

  String _formatRequiredDuration(int seconds) {
     if (seconds < 60) {
      return "$seconds seconds";
    } else {
      final minutes = (seconds / 60).ceil(); // Round up for "at least X minute"
      return "$minutes minute${minutes > 1 ? 's' : ''}";
    }
  }

  @override
  Widget build(BuildContext context) {
    return Dialog(
      backgroundColor: Colors.transparent,
      child: Container(
        padding: const EdgeInsets.all(24),
        decoration: BoxDecoration(
          color: const Color(0xFF1E2746), // Dark Blue/Grey background
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
             BoxShadow(
              color: Colors.black.withOpacity(0.5),
              blurRadius: 20,
              offset: const Offset(0, 10),
            ),
          ],
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            // Title: Played X Seconds
            Text(
              "Played ${_formatDuration(playedSeconds)}",
              textAlign: TextAlign.center,
              style: GoogleFonts.poppins(
                fontSize: 18,
                fontWeight: FontWeight.bold,
                color: Colors.white,
              ),
            ),
            const SizedBox(height: 16),
            
            // Body: Please play for at least X minute...
            Container(
               padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
               decoration: BoxDecoration(
                 color: const Color(0xFF2A3655), // Lighter dark background
                 borderRadius: BorderRadius.circular(8),
               ),
               child: Text(
                "Please play for at least ${_formatRequiredDuration(requiredSeconds)} to receive $rewardAmount tokens.",
                textAlign: TextAlign.center,
                style: GoogleFonts.poppins(
                  fontSize: 14,
                  color: Colors.white70,
                  height: 1.5,
                ),
              ),
            ),
            
            const SizedBox(height: 24),
            
            // Close Button
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: onClose,
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFFE53935), // Red color
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 12),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                  elevation: 2,
                ),
                child: Text(
                  "Close",
                  style: GoogleFonts.poppins(
                    fontWeight: FontWeight.w600,
                    fontSize: 16,
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    ).animate().scale(duration: 300.ms, curve: Curves.easeOutBack);
  }
}
