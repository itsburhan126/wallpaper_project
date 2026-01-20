import 'package:flutter/material.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:google_fonts/google_fonts.dart';

class RewardDialog extends StatefulWidget {
  final int rewardAmount;
  final Future<void> Function() onReceive;
  final VoidCallback onClose;
  final String currencySymbol;
  final int coinRate;

  const RewardDialog({
    super.key,
    required this.rewardAmount,
    required this.onReceive,
    required this.onClose,
    this.currencySymbol = '\$',
    this.coinRate = 1000,
  });

  @override
  State<RewardDialog> createState() => _RewardDialogState();
}

class _RewardDialogState extends State<RewardDialog> {
  bool _isLoading = false;

  void _handleReceive() async {
    setState(() {
      _isLoading = true;
    });
    
    // Call the async callback
    await widget.onReceive();
    
    // Only reset if still mounted (though usually the dialog is closed by then)
    if (mounted) {
      setState(() {
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final double value = widget.coinRate > 0 
        ? widget.rewardAmount / widget.coinRate 
        : 0.0;

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
            padding: const EdgeInsets.fromLTRB(24, 60, 24, 24),
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
                // Coin Icon & Amount
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Container(
                      padding: const EdgeInsets.all(4),
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: Colors.amber.withOpacity(0.2),
                      ),
                      child: const Icon(Icons.monetization_on_rounded, size: 48, color: Colors.amber),
                    )
                    .animate(onPlay: (c) => c.repeat(reverse: true))
                    .scale(duration: 1000.ms, begin: const Offset(0.9, 0.9), end: const Offset(1.1, 1.1)),
                    const SizedBox(width: 12),
                    Text(
                      "+${widget.rewardAmount}",
                      style: GoogleFonts.poppins(
                        fontSize: 42,
                        fontWeight: FontWeight.w900,
                        color: Colors.orange.shade800,
                        letterSpacing: -1,
                      ),
                    ),
                  ],
                ),
                
                // Approx Value Tag
                Container(
                  margin: const EdgeInsets.only(top: 8, bottom: 24),
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                  decoration: BoxDecoration(
                    color: Colors.green.shade500,
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Text(
                    "≈ ${widget.currencySymbol}${value.toStringAsFixed(4)}",
                    style: const TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.bold,
                      fontSize: 16,
                    ),
                  ),
                ),

                // Receive Button
                SizedBox(
                  width: double.infinity,
                  height: 56,
                  child: ElevatedButton(
                    onPressed: _isLoading ? null : _handleReceive,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.deepOrange,
                      foregroundColor: Colors.white,
                      disabledBackgroundColor: Colors.deepOrange, // Keep background color when loading
                      disabledForegroundColor: Colors.white, // Keep text color when loading
                      elevation: 4,
                      shadowColor: Colors.deepOrange.withOpacity(0.4),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(30)),
                      padding: EdgeInsets.zero,
                    ),
                    child: _isLoading 
                        ? Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              const SizedBox(
                                width: 20,
                                height: 20,
                                child: CircularProgressIndicator(
                                  color: Colors.white,
                                  strokeWidth: 2,
                                ),
                              ),
                              const SizedBox(width: 12),
                              Text(
                                "Loading...",
                                style: GoogleFonts.poppins(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                  letterSpacing: 0.5,
                                ),
                              ),
                            ],
                          )
                        : Text(
                            "Receive",
                            style: GoogleFonts.poppins(
                              fontSize: 18,
                              fontWeight: FontWeight.bold,
                              letterSpacing: 0.5,
                            ),
                          ),
                  ),
                ),
              ],
            ),
          ),

          // Ribbon Header
          Positioned(
            top: -24,
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 12),
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: [Colors.amber.shade300, Colors.orange.shade300],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(16),
                boxShadow: [
                  BoxShadow(
                    color: Colors.orange.withOpacity(0.3),
                    blurRadius: 10,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: Text(
                "Congratulations",
                style: GoogleFonts.poppins(
                  color: Colors.white,
                  fontWeight: FontWeight.bold,
                  fontSize: 20,
                  letterSpacing: 0.5,
                  shadows: [
                    Shadow(
                      color: Colors.black.withOpacity(0.1),
                      offset: const Offset(0, 2),
                      blurRadius: 2,
                    ),
                  ],
                ),
              ),
            )
            .animate()
            .slideY(begin: -0.5, end: 0, duration: 600.ms, curve: Curves.elasticOut)
            .fadeIn(duration: 400.ms),
          ),

          // Close Button
          Positioned(
            top: -12,
            right: 0,
            child: GestureDetector(
              onTap: widget.onClose,
              child: Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: Colors.black.withOpacity(0.5),
                  shape: BoxShape.circle,
                  border: Border.all(color: Colors.white, width: 2),
                ),
                child: const Icon(Icons.close, color: Colors.white, size: 20),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
