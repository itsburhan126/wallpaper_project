import 'package:flutter/material.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../providers/language_provider.dart';

class LuckyWheelWinDialog extends StatefulWidget {
  final int rewardAmount;
  final Future<void> Function() onClaim;
  final Future<void> Function() onClaim2x;
  final VoidCallback onClose;

  const LuckyWheelWinDialog({
    super.key,
    required this.rewardAmount,
    required this.onClaim,
    required this.onClaim2x,
    required this.onClose,
  });

  @override
  State<LuckyWheelWinDialog> createState() => _LuckyWheelWinDialogState();
}

class _LuckyWheelWinDialogState extends State<LuckyWheelWinDialog> {
  bool _isLoading = false;
  bool _is2xLoading = false;

  void _handleClaim() async {
    setState(() => _isLoading = true);
    await widget.onClaim();
    if (mounted) {
      setState(() => _isLoading = false);
      widget.onClose();
    }
  }

  void _handleClaim2x() async {
    setState(() => _is2xLoading = true);
    await widget.onClaim2x();
    if (mounted) {
      setState(() => _is2xLoading = false);
      widget.onClose();
    }
  }

  @override
  Widget build(BuildContext context) {
    return Consumer<LanguageProvider>(
      builder: (context, langProvider, child) {
        return Dialog(
          backgroundColor: Colors.transparent,
          insetPadding: const EdgeInsets.symmetric(horizontal: 20),
          child: Stack(
            alignment: Alignment.center,
            clipBehavior: Clip.none,
            children: [
              // Main Card (Dark/Gold Theme for Lucky Wheel)
              Container(
                width: double.infinity,
                padding: const EdgeInsets.fromLTRB(24, 60, 24, 24),
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    colors: [Color(0xFF263238), Color(0xFF212121)], // Dark gradient
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  borderRadius: BorderRadius.circular(24),
                  border: Border.all(color: const Color(0xFFFFD54F), width: 2), // Gold Border
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withValues(alpha: 0.5),
                      blurRadius: 20,
                      offset: const Offset(0, 10),
                    ),
                    BoxShadow(
                      color: Colors.amber.withValues(alpha: 0.2),
                      blurRadius: 10,
                      spreadRadius: 2,
                    ),
                  ],
                ),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Text(
                      langProvider.getText('jackpot'),
                      style: GoogleFonts.poppins(
                        fontSize: 28,
                        fontWeight: FontWeight.bold,
                        color: Colors.white,
                        letterSpacing: 1.5,
                        shadows: [
                          const Shadow(color: Colors.amber, blurRadius: 10),
                        ],
                      ),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      langProvider.getText('won_rewards'),
                      textAlign: TextAlign.center,
                      style: GoogleFonts.poppins(
                        fontSize: 14,
                        color: Colors.white70,
                      ),
                    ),
                    const SizedBox(height: 24),
                    
                    // Reward Display
                    Container(
                      padding: const EdgeInsets.symmetric(vertical: 20, horizontal: 30),
                      decoration: BoxDecoration(
                        color: Colors.black45,
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(color: Colors.amber.withValues(alpha: 0.5)),
                      ),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          const Icon(Icons.monetization_on_rounded, size: 48, color: Colors.amber)
                              .animate(onPlay: (c) => c.repeat(reverse: true))
                              .scale(duration: 800.ms, begin: const Offset(0.9, 0.9), end: const Offset(1.1, 1.1))
                              .shimmer(duration: 1500.ms, color: Colors.white),
                          const SizedBox(width: 16),
                          Text(
                            "+${widget.rewardAmount}",
                            style: GoogleFonts.poppins(
                              fontSize: 42,
                              fontWeight: FontWeight.w900,
                              color: const Color(0xFFFFD54F),
                              shadows: [
                                const Shadow(color: Colors.orange, blurRadius: 8, offset: Offset(0, 2)),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                    
                    const SizedBox(height: 32),

                    // 2x Button (Primary)
                    SizedBox(
                      width: double.infinity,
                      height: 60,
                      child: ElevatedButton(
                        onPressed: (_isLoading || _is2xLoading) ? null : _handleClaim2x,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFFFF6F00), // Amber Dark
                          foregroundColor: Colors.white,
                          elevation: 8,
                          shadowColor: Colors.amber.withValues(alpha: 0.5),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                          padding: EdgeInsets.zero,
                        ),
                        child: _is2xLoading 
                            ? Row(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  const SizedBox(
                                    width: 24, height: 24,
                                    child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2),
                                  ),
                                  const SizedBox(width: 12),
                                  Text(
                                    langProvider.getText('loading_ad'),
                                    style: GoogleFonts.poppins(
                                      fontSize: 18,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                ],
                              )
                            : Row(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  const Icon(Icons.play_circle_fill, size: 28),
                                  const SizedBox(width: 12),
                                  Column(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        langProvider.getText('claim_2x'),
                                        style: GoogleFonts.poppins(
                                          fontSize: 18,
                                          fontWeight: FontWeight.bold,
                                          height: 1,
                                        ),
                                      ),
                                      Text(
                                        "+${widget.rewardAmount * 2} ${langProvider.getText('coins')}",
                                        style: GoogleFonts.poppins(
                                          fontSize: 12,
                                          color: Colors.white.withValues(alpha: 0.9),
                                          height: 1,
                                        ),
                                      ),
                                    ],
                                  ),
                                ],
                              ),
                      ),
                    ).animate(onPlay: (c) => c.repeat(reverse: true))
                    .shimmer(duration: 2000.ms, color: Colors.white.withValues(alpha: 0.4)),

                    const SizedBox(height: 16),

                    // Normal Claim Button (Secondary)
                    SizedBox(
                      width: double.infinity,
                      height: 48,
                      child: TextButton(
                        onPressed: (_isLoading || _is2xLoading) ? null : _handleClaim,
                        style: TextButton.styleFrom(
                          foregroundColor: Colors.white54,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                        ),
                        child: _isLoading
                            ? const SizedBox(
                                width: 20, height: 20,
                                child: CircularProgressIndicator(color: Colors.white54, strokeWidth: 2),
                              )
                            : Text(
                                langProvider.getText('no_thanks_claim_fmt').replaceAll('@amount', '${widget.rewardAmount}'),
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
                top: -25,
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 30, vertical: 12),
                  decoration: BoxDecoration(
                    gradient: const LinearGradient(
                      colors: [Color(0xFFD50000), Color(0xFFB71C1C)], // Red Ribbon
                    ),
                    borderRadius: BorderRadius.circular(30),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withValues(alpha: 0.4),
                        blurRadius: 10,
                        offset: const Offset(0, 5),
                      ),
                    ],
                    border: Border.all(color: const Color(0xFFFFD54F), width: 2),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      const Icon(Icons.star, color: Colors.yellow, size: 24),
                      const SizedBox(width: 8),
                      Text(
                        langProvider.getText('congratulations_caps'),
                        style: GoogleFonts.poppins(
                          color: Colors.white,
                          fontWeight: FontWeight.bold,
                          fontSize: 16,
                          letterSpacing: 1.2,
                        ),
                      ),
                      const SizedBox(width: 8),
                      const Icon(Icons.star, color: Colors.yellow, size: 24),
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
    );
  }
}
