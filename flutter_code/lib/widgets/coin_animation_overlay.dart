import 'dart:math';
import 'package:flutter/material.dart';

class CoinAnimationOverlay {
  static void show(BuildContext context, GlobalKey targetKey, {int coinCount = 10, VoidCallback? onComplete}) {
    final overlay = Overlay.of(context);
    final renderBox = targetKey.currentContext?.findRenderObject() as RenderBox?;
    
    if (renderBox == null) {
      onComplete?.call();
      return;
    }

    final targetPosition = renderBox.localToGlobal(Offset.zero);
    final targetSize = renderBox.size;
    final targetCenter = targetPosition + Offset(targetSize.width / 2, targetSize.height / 2);

    final size = MediaQuery.of(context).size;
    final startPosition = Offset(size.width / 2, size.height / 2);

    // Create multiple coins
    for (int i = 0; i < coinCount; i++) {
      // Pass onComplete only to the last coin
      final isLast = i == coinCount - 1;
      _animateCoin(
        context, 
        overlay, 
        startPosition, 
        targetCenter, 
        i * 100, 
        isLast ? onComplete : null
      );
    }
  }

  static void _animateCoin(
    BuildContext context, 
    OverlayState overlay, 
    Offset startPos, 
    Offset endPos, 
    int delayMs,
    VoidCallback? onComplete,
  ) async {
    late OverlayEntry entry;
    
    // Randomize path slightly for natural effect
    final random = Random();
    final controlPointOffset = Offset(
      (random.nextDouble() - 0.5) * 100, // Random X spread
      (random.nextDouble() - 0.5) * 100, // Random Y spread
    );

    entry = OverlayEntry(
      builder: (context) {
        return _FlyingCoin(
          startPos: startPos,
          endPos: endPos,
          controlOffset: controlPointOffset,
          onComplete: () {
            entry.remove();
            onComplete?.call();
          },
        );
      },
    );

    await Future.delayed(Duration(milliseconds: delayMs));
    if (overlay.mounted) {
       overlay.insert(entry);
    }
  }
}

class _FlyingCoin extends StatefulWidget {
  final Offset startPos;
  final Offset endPos;
  final Offset controlOffset;
  final VoidCallback onComplete;

  const _FlyingCoin({
    required this.startPos,
    required this.endPos,
    required this.controlOffset,
    required this.onComplete,
  });

  @override
  State<_FlyingCoin> createState() => _FlyingCoinState();
}

class _FlyingCoinState extends State<_FlyingCoin> with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<double> _animation;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 800),
    );

    _animation = CurvedAnimation(parent: _controller, curve: Curves.easeInBack);

    _controller.forward().then((_) {
      widget.onComplete();
    });
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return AnimatedBuilder(
      animation: _animation,
      builder: (context, child) {
        final t = _animation.value;
        // Quadratic Bezier Curve: B(t) = (1-t)^2 * P0 + 2(1-t)t * P1 + t^2 * P2
        // P0 = start, P2 = end, P1 = control point (midpoint + random offset)
        
        final p0 = widget.startPos;
        final p2 = widget.endPos;
        final p1 = Offset(
          (p0.dx + p2.dx) / 2 + widget.controlOffset.dx,
          (p0.dy + p2.dy) / 2 + widget.controlOffset.dy,
        );

        final x = pow(1 - t, 2) * p0.dx + 2 * (1 - t) * t * p1.dx + pow(t, 2) * p2.dx;
        final y = pow(1 - t, 2) * p0.dy + 2 * (1 - t) * t * p1.dy + pow(t, 2) * p2.dy;

        // Scale down as it gets closer
        final scale = 1.0 - (t * 0.5); 
        final opacity = t > 0.9 ? (1.0 - t) * 10 : 1.0; // Fade out at very end

        return Positioned(
          left: x - 12, // Center the 24px coin
          top: y - 12,
          child: Opacity(
            opacity: opacity.clamp(0.0, 1.0),
            child: Transform.scale(
              scale: scale,
              child: Container(
                width: 24,
                height: 24,
                decoration: const BoxDecoration(
                  shape: BoxShape.circle,
                  color: Colors.amber,
                  boxShadow: [
                    BoxShadow(
                      color: Colors.orange,
                      blurRadius: 4,
                      spreadRadius: 1,
                    )
                  ],
                ),
                child: const Icon(Icons.monetization_on, color: Colors.white, size: 16),
              ),
            ),
          ),
        );
      },
    );
  }
}
