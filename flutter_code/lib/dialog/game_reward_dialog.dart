import 'package:flutter/material.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:google_fonts/google_fonts.dart';

class GameRewardDialog extends StatefulWidget {
  final int baseReward;
  final Future<void> Function() onClaim;
  final Future<void> Function() onClaim2x;
  final VoidCallback onClose;
  final String currencySymbol;

  const GameRewardDialog({
    super.key,
    required this.baseReward,
    required this.onClaim,
    required this.onClaim2x,
    required this.onClose,
    this.currencySymbol = '\$',
  });

  @override
  State<GameRewardDialog> createState() => _GameRewardDialogState();
}

class _GameRewardDialogState extends State<GameRewardDialog> {
  bool _isLoading = false;
  bool _is2xLoading = false;

  void _handleClaim() async {
    setState(() => _isLoading = true);
    await widget.onClaim();
    if (mounted) setState(() => _isLoading = false);
  }

  void _handleClaim2x() async {
    setState(() => _is2xLoading = true);
    await widget.onClaim2x();
    if (mounted) setState(() => _is2xLoading = false);
  }

  @override
  Widget build(BuildContext context) {
    return Dialog(
      backgroundColor: Colors.transparent,
      insetPadding: const EdgeInsets.symmetric(horizontal: 20),
      child: Stack(
        alignment: Alignment.center,
        clipBehavior: Clip.none,
        children: [
          // Main Card
          Container(
            width: double.infinity,
            padding: const EdgeInsets.fromLTRB(24, 50, 24, 24),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(24),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.2),
                  blurRadius: 20,
                  offset: const Offset(0, 10),
                ),
              ],
            ),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(
                  "Level Completed!",
                  style: GoogleFonts.poppins(
                    fontSize: 22,
                    fontWeight: FontWeight.bold,
                    color: Colors.black87,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  "You've earned coins",
                  style: GoogleFonts.poppins(
                    fontSize: 14,
                    color: Colors.black54,
                  ),
                ),
                const SizedBox(height: 20),
                
                // Coin Icon & Amount
                Container(
                  padding: const EdgeInsets.symmetric(vertical: 16, horizontal: 24),
                  decoration: BoxDecoration(
                    color: Colors.amber.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(color: Colors.amber.withOpacity(0.3)),
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      const Icon(Icons.monetization_on_rounded, size: 40, color: Colors.amber)
                          .animate(onPlay: (c) => c.repeat(reverse: true))
                          .scale(duration: 1000.ms, begin: const Offset(0.9, 0.9), end: const Offset(1.1, 1.1)),
                      const SizedBox(width: 12),
                      Text(
                        "+${widget.baseReward}",
                        style: GoogleFonts.poppins(
                          fontSize: 36,
                          fontWeight: FontWeight.w900,
                          color: Colors.orange.shade800,
                          letterSpacing: -1,
                        ),
                      ),
                    ],
                  ),
                ),
                
                const SizedBox(height: 24),

                // 2x Button (Primary)
                SizedBox(
                  width: double.infinity,
                  height: 56,
                  child: ElevatedButton(
                    onPressed: (_isLoading || _is2xLoading) ? null : _handleClaim2x,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.deepPurple,
                      foregroundColor: Colors.white,
                      disabledBackgroundColor: Colors.deepPurple,
                      disabledForegroundColor: Colors.white,
                      elevation: 4,
                      shadowColor: Colors.deepPurple.withOpacity(0.4),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                      padding: EdgeInsets.zero,
                    ),
                    child: _is2xLoading 
                        ? const SizedBox(
                            width: 24, height: 24,
                            child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2),
                          )
                        : Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              const Icon(Icons.play_circle_fill, size: 24),
                              const SizedBox(width: 8),
                              Column(
                                mainAxisAlignment: MainAxisAlignment.center,
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    "CLAIM 2X",
                                    style: GoogleFonts.poppins(
                                      fontSize: 16,
                                      fontWeight: FontWeight.bold,
                                      height: 1,
                                    ),
                                  ),
                                  Text(
                                    "+${widget.baseReward * 2} Coins",
                                    style: GoogleFonts.poppins(
                                      fontSize: 10,
                                      color: Colors.white70,
                                      height: 1,
                                    ),
                                  ),
                                ],
                              ),
                            ],
                          ),
                  ),
                ).animate(onPlay: (c) => c.repeat(reverse: true))
                .shimmer(duration: 2000.ms, color: Colors.white.withOpacity(0.2)),

                const SizedBox(height: 12),

                // Normal Claim Button (Secondary)
                SizedBox(
                  width: double.infinity,
                  height: 48,
                  child: TextButton(
                    onPressed: (_isLoading || _is2xLoading) ? null : _handleClaim,
                    style: TextButton.styleFrom(
                      foregroundColor: Colors.grey.shade600,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                    ),
                    child: _isLoading
                        ? const SizedBox(
                            width: 20, height: 20,
                            child: CircularProgressIndicator(color: Colors.grey, strokeWidth: 2),
                          )
                        : Text(
                            "No thanks, claim ${widget.baseReward}",
                            style: GoogleFonts.poppins(
                              fontSize: 14,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                  ),
                ),
              ],
            ),
          ),

          // Ribbon Header
          Positioned(
            top: -20,
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 10),
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: [Colors.blue.shade400, Colors.purple.shade400],
                ),
                borderRadius: BorderRadius.circular(20),
                boxShadow: [
                  BoxShadow(
                    color: Colors.purple.withOpacity(0.3),
                    blurRadius: 10,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  const Icon(Icons.star, color: Colors.yellow, size: 20),
                  const SizedBox(width: 8),
                  Text(
                    "CONGRATULATIONS",
                    style: GoogleFonts.poppins(
                      color: Colors.white,
                      fontWeight: FontWeight.bold,
                      fontSize: 16,
                      letterSpacing: 1,
                    ),
                  ),
                  const SizedBox(width: 8),
                  const Icon(Icons.star, color: Colors.yellow, size: 20),
                ],
              ),
            )
            .animate()
            .slideY(begin: -0.5, end: 0, duration: 600.ms, curve: Curves.elasticOut)
            .fadeIn(duration: 400.ms),
          ),
        ],
      ),
    );
  }
}
