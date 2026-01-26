import 'package:flutter/material.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../../utils/app_theme.dart';
import '../../providers/language_provider.dart';

class ProfessionalToast {
  static void show(BuildContext context, {required int coinAmount}) {
    _showToast(context, _ToastWidget(coinAmount: coinAmount, type: ToastType.success));
  }

  static void showSuccess(BuildContext context, {required String message}) {
    _showToast(context, _ToastWidget(message: message, type: ToastType.success));
  }

  static void showError(BuildContext context, {required String message}) {
    _showToast(context, _ToastWidget(message: message, type: ToastType.error));
  }

  static void showLoading(BuildContext context, {required String message}) {
    _showToast(context, _ToastWidget(message: message, type: ToastType.loading));
  }

  static void _showToast(BuildContext context, Widget child) {
    final overlay = Overlay.of(context);
    late OverlayEntry entry;
    final bottomPadding = MediaQuery.of(context).padding.bottom;

    entry = OverlayEntry(
      builder: (context) => Positioned(
        bottom: bottomPadding + 50, // Respect Safe Area
        left: 20,
        right: 20,
        child: Material(
          type: MaterialType.transparency,
          child: child,
        ),
      ),
    );

    overlay.insert(entry);
    
    // Auto remove after duration
    Future.delayed(const Duration(seconds: 4), () { // Increased duration slightly
      if (entry.mounted) {
        entry.remove();
      }
    });
  }
}

enum ToastType { success, error, loading }

class _ToastWidget extends StatelessWidget {
  final int? coinAmount;
  final String? message;
  final ToastType type;

  const _ToastWidget({
    this.coinAmount,
    this.message,
    required this.type,
  });

  @override
  Widget build(BuildContext context) {
    final langProvider = Provider.of<LanguageProvider>(context, listen: false);
    final isError = type == ToastType.error;
    final isLoading = type == ToastType.loading;
    final primaryColor = isError ? Colors.redAccent : (isLoading ? Colors.blueAccent : Colors.amber);
    final icon = isError ? Icons.error_outline_rounded : (isLoading ? Icons.hourglass_empty_rounded : Icons.check_rounded);
    
    String titleText;
    if (isError) {
      titleText = langProvider.getText('oops');
    } else if (isLoading) {
      titleText = langProvider.getText('please_wait');
    } else if (message != null && coinAmount == null) {
      titleText = langProvider.getText('success');
    } else {
      titleText = langProvider.getText('reward_claimed');
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [
            const Color(0xFF2A1B4E).withValues(alpha: 0.95),
            AppTheme.darkBackgroundColor.withValues(alpha: 0.95),
          ],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: primaryColor.withValues(alpha: 0.3), width: 1),
        boxShadow: [
          BoxShadow(
            color: primaryColor.withValues(alpha: 0.1),
            blurRadius: 16,
            offset: const Offset(0, 4),
          ),
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.4),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Row(
        children: [
          // Animated Icon Container
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: primaryColor.withValues(alpha: 0.1),
              shape: BoxShape.circle,
              border: Border.all(color: primaryColor.withValues(alpha: 0.5)),
            ),
            child: Icon(icon, color: primaryColor, size: 24)
                .animate(onPlay: (c) => c.repeat(reverse: true))
                .scale(begin: const Offset(0.8, 0.8), end: const Offset(1.2, 1.2), duration: 1000.ms),
          ),
          const SizedBox(width: 16),
          
          // Text Content
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(
                  titleText,
                  style: GoogleFonts.poppins(
                    color: Colors.white,
                    fontWeight: FontWeight.bold,
                    fontSize: 16,
                  ),
                ),
                const SizedBox(height: 4),
                if (isError || (message != null && coinAmount == null))
                  Text(
                    message ?? langProvider.getText('something_went_wrong'),
                    style: GoogleFonts.poppins(
                      color: Colors.white70,
                      fontSize: 12,
                    ),
                  )
                else
                  Row(
                    children: [
                      Text(
                        langProvider.getText('you_received'),
                        style: GoogleFonts.poppins(
                      color: Colors.white70,
                        fontSize: 12,
                        ),
                      ),
                      const SizedBox(width: 4),
                      Text(
                        "$coinAmount ${langProvider.getText('coins_suffix')}",
                        style: GoogleFonts.poppins(
                          color: Colors.amber,
                          fontWeight: FontWeight.bold,
                          fontSize: 13,
                        ),
                      ),
                    ],
                  ),
              ],
            ),
          ),
        ],
      ),
    )
    .animate()
    .slideY(begin: 1, end: 0, duration: 600.ms, curve: Curves.elasticOut) // Slide up from bottom
    .fadeIn(duration: 400.ms)
    .then(delay: 2000.ms)
    .slideY(begin: 0, end: 1, duration: 400.ms, curve: Curves.easeIn) // Slide down to exit
    .fadeOut(duration: 300.ms);
  }
}
