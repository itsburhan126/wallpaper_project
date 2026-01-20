import 'dart:async';
import 'dart:math' as math;
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:wallpaper_app/providers/app_provider.dart';
import 'package:wallpaper_app/services/google_ad_service.dart';
import 'package:google_fonts/google_fonts.dart';
import '../dialog/reward_dialog.dart';
import '../widgets/coin_animation_overlay.dart';
import '../widgets/animated_coin_balance.dart';
import '../widgets/toast/professional_toast.dart';

class LuckyWheelDialog extends StatefulWidget {
  const LuckyWheelDialog({Key? key}) : super(key: key);

  @override
  State<LuckyWheelDialog> createState() => _LuckyWheelDialogState();
}

class _LuckyWheelDialogState extends State<LuckyWheelDialog> with TickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<double> _animation;
  
  // For the "lights" blinking effect
  late AnimationController _lightsController;
  
  // For the Spin Button pulse effect
  late AnimationController _pulseController;
  late Animation<double> _pulseAnimation;
  
  // Coin Animation Target
  final GlobalKey _coinIconKey = GlobalKey();

  bool _isSpinning = false;
  final GoogleAdService _adService = GoogleAdService();

  @override
  void initState() {
    super.initState();
    // Wheel Animation
    _controller = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 4),
    );
    _animation = CurvedAnimation(parent: _controller, curve: Curves.decelerate);
    
    // Lights Blinking Animation
    _lightsController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 500),
    )..repeat(reverse: true);

    // Button Pulse Animation
    _pulseController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1000),
    )..repeat(reverse: true);
    
    _pulseAnimation = Tween<double>(begin: 1.0, end: 1.05).animate(
      CurvedAnimation(parent: _pulseController, curve: Curves.easeInOut),
    );

    // Preload Ads
    _adService.loadRewardedAd(context);
    _adService.loadInterstitialAd(context);
  }

  @override
  void dispose() {
    _controller.dispose();
    _lightsController.dispose();
    _pulseController.dispose();
    super.dispose();
  }

  void _spinWheel() {
    if (_isSpinning) return;

    final appProvider = Provider.of<AppProvider>(context, listen: false);
    if (appProvider.luckyWheelSpinsCount >= appProvider.luckyWheelLimit) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Daily limit reached! Come back tomorrow.')),
      );
      return;
    }

    setState(() {
      _isSpinning = true;
    });

    // Randomize result
    final random = math.Random();
    int targetIndex = random.nextInt(8); // 0-7
    
    // Calculate rotation
    double anglePerSlice = 2 * math.pi / 8;
    double extraSpins = 5 * 2 * math.pi; // 5 full spins
    
    // Target calculation:
    // We want the POINTER (top, -pi/2) to point to targetIndex.
    // If rotation is 0, index 0 is at -pi/2 - sliceAngle/2 (start) to -pi/2 + sliceAngle/2.
    // Actually, let's look at the Painter.
    // Painter draws index 0 at: -pi/2 - (sliceAngle/2).
    // So Center of index 0 is at -pi/2.
    // To land index i at -pi/2, we need to rotate by:
    // rotation = - (i * anglePerSlice).
    // Since we spin forward (positive), we need:
    // rotation = 2*pi - (i * anglePerSlice) + extraSpins.
    
    double targetAngle = extraSpins + (2 * math.pi) - (targetIndex * anglePerSlice);
    
    // Add randomness (+/- 40% of slice)
    double randomOffset = (random.nextDouble() - 0.5) * (anglePerSlice * 0.8);
    targetAngle += randomOffset;

    _controller.duration = const Duration(seconds: 4);
    _controller = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 4),
      upperBound: targetAngle,
    );
    
    _animation = CurvedAnimation(parent: _controller, curve: Curves.easeOutCubic);

    _controller.forward().then((_) {
      _handleSpinComplete(targetIndex);
    });
  }

  void _handleSpinComplete(int index) async {
    final appProvider = Provider.of<AppProvider>(context, listen: false);
    int reward = appProvider.luckyWheelRewards[index];
    
    // Note: We do NOT increment spin count here anymore.
    // It will be done when the user claims the reward.
    
    if (mounted) {
      setState(() {
        _isSpinning = false;
      });
      
      _showWinDialog(reward);
    }
  }

  void _showWinDialog(int reward) {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (ctx) => RewardDialog(
        rewardAmount: reward,
        onReceive: () async {
          final appProvider = Provider.of<AppProvider>(context, listen: false);
          
          // Show Ad before giving reward
          // We don't add coins in callbacks anymore to ensure correct animation timing
          bool earned = await _adService.showRewardedAd(
            context,
            onReward: (_) {
               // Marked as earned internally
            },
            onFailure: () {
              if (mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(content: Text("Ad failed. Claiming normal reward.")),
                );
              }
            },
          );
          
          // Close the dialog first
          if (mounted && ctx.mounted) {
            Navigator.pop(ctx);
          }
          
          // Show animation and add coins (whether ad was shown or failed fallback)
          if (mounted) {
             // Small delay to let dialog close smoothly
             await Future.delayed(const Duration(milliseconds: 200));
             
             if (mounted) {
               CoinAnimationOverlay.show(
                  context, 
                  _coinIconKey, 
                  coinCount: 10,
                  onComplete: () {
                     appProvider.addCoins(reward);
                     appProvider.incrementLuckyWheelSpinsCount();
                     if (mounted) {
                        ProfessionalToast.showSuccess(
                           context, 
                           message: "You won $reward coins!",
                        );
                     }
                  }
               );
             }
          }
          
          if (mounted) {
            // Preload next ad
            _adService.loadRewardedAd(context);
          }
        },
        onClose: () {
          Navigator.pop(ctx);
          // If they close without claiming, we DO NOT deduct the spin?
          // Or should we? The user instruction implies spin end happens after claim.
          // If they close, they lose the reward but also keep the spin? 
          // Usually better to force claim or auto-claim on close, but for now sticking to "receive click" logic.
          _adService.loadRewardedAd(context);
        },
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final appProvider = Provider.of<AppProvider>(context);
    final rewards = appProvider.luckyWheelRewards;
    final safeRewards = rewards.length == 8 ? rewards : List.generate(8, (i) => (i + 1) * 10);

    return Scaffold(
      backgroundColor: Colors.transparent,
      body: Stack(
        children: [
          // Dark Background Overlay (Dimmed to show Task Page)
          GestureDetector(
            onTap: () => Navigator.pop(context),
            child: Container(
              color: Colors.black.withOpacity(0.7), // Standard dialog dimming
            ),
          ),
          
          // Main Content
          Center(
            child: SingleChildScrollView(
              clipBehavior: Clip.none,
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  // Ribbon Header
                  Stack(
                    alignment: Alignment.center,
                    children: [
                      // 2. The Ribbon
                      CustomPaint(
                        size: const Size(300, 80),
                        painter: RibbonPainter(),
                        child: Container(
                          width: 300,
                          height: 80,
                          alignment: Alignment.center,
                          padding: const EdgeInsets.only(bottom: 10), // Adjust for ribbon curve
                          child: const Text(
                            "LUCKY SPIN",
                            style: TextStyle(
                              fontFamily: 'Roboto', // Use a standard font or one from assets if available
                              color: Colors.white,
                              fontSize: 28,
                              fontWeight: FontWeight.w900,
                              letterSpacing: 2.0,
                              shadows: [
                                Shadow(color: Color(0xFF4A148C), offset: Offset(2, 2), blurRadius: 0),
                                Shadow(color: Colors.black26, offset: Offset(0, 4), blurRadius: 4),
                              ],
                            ),
                          ),
                        ),
                      ),
                      
                      // 3. Floating Riches (Top Decoration)
                      Positioned(
                        top: -25,
                        child: SizedBox(
                          width: 200,
                          height: 60,
                          child: Stack(
                            alignment: Alignment.bottomCenter,
                            children: [
                              // Back row
                              Positioned(
                                left: 60,
                                bottom: 5,
                                child: Transform.rotate(
                                  angle: -0.2,
                                  child: const Icon(Icons.diamond, color: Colors.blueAccent, size: 30),
                                ),
                              ),
                              Positioned(
                                right: 60,
                                bottom: 5,
                                child: Transform.rotate(
                                  angle: 0.2,
                                  child: const Icon(Icons.diamond, color: Colors.pinkAccent, size: 30),
                                ),
                              ),
                              // Front row
                              Positioned(
                                bottom: 0,
                                child: Container(
                                  decoration: const BoxDecoration(
                                    shape: BoxShape.circle,
                                    boxShadow: [BoxShadow(color: Colors.black26, blurRadius: 10, offset: Offset(0, 5))],
                                  ),
                                  child: const Icon(Icons.monetization_on, color: Colors.amber, size: 50),
                                ),
                              ),
                              Positioned(
                                left: 40,
                                bottom: -5,
                                child: Transform.rotate(
                                  angle: -0.5,
                                  child: const Icon(Icons.monetization_on, color: Colors.amberAccent, size: 35),
                                ),
                              ),
                              Positioned(
                                right: 40,
                                bottom: -5,
                                child: Transform.rotate(
                                  angle: 0.5,
                                  child: const Icon(Icons.monetization_on, color: Colors.amberAccent, size: 35),
                                ),
                              ),
                              
                              // Sparkles
                              const Positioned(
                                top: 0,
                                left: 80,
                                child: Icon(Icons.star, color: Colors.white, size: 15),
                              ),
                              const Positioned(
                                top: 10,
                                right: 70,
                                child: Icon(Icons.star, color: Colors.white, size: 12),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                  
                  const SizedBox(height: 30),
                  
                  // The Wheel
                  SizedBox(
                    height: 340,
                    width: 340,
                    child: Stack(
                      alignment: Alignment.center,
                      children: [
                        // Back Glow
                        Container(
                          width: 320,
                          height: 320,
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            boxShadow: [
                              BoxShadow(
                                color: Colors.purple.withOpacity(0.6),
                                blurRadius: 60,
                                spreadRadius: 10,
                              ),
                            ],
                          ),
                        ),
                        
                        // Wheel Paint
                        AnimatedBuilder(
                          animation: Listenable.merge([_animation, _lightsController]),
                          builder: (context, child) {
                            return Transform.rotate(
                              angle: _controller.value,
                              child: CustomPaint(
                                size: const Size(320, 320),
                                painter: ProfessionalWheelPainter(
                                  rewards: safeRewards,
                                  lightsOn: _lightsController.value > 0.5,
                                ),
                              ),
                            );
                          },
                        ),
                        
                        // Center Hub
                        Container(
                          width: 60,
                          height: 60,
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            gradient: const RadialGradient(
                              colors: [Color(0xFFFFD54F), Color(0xFFFF6F00)],
                            ),
                            border: Border.all(color: Colors.white, width: 2),
                            boxShadow: [
                              BoxShadow(color: Colors.black.withOpacity(0.5), blurRadius: 10, offset: const Offset(0, 5)),
                            ],
                          ),
                          child: const Icon(Icons.casino, color: Colors.white, size: 30),
                        ),
                        
                        // Top Pointer
                        Positioned(
                          top: 0,
                          child: Transform.translate(
                            offset: const Offset(0, -10),
                            child: Container(
                              height: 50,
                              width: 40,
                              child: Stack(
                                alignment: Alignment.topCenter,
                                children: [
                                  // Pin Body
                                  Container(
                                    margin: const EdgeInsets.only(top: 5),
                                    child: Icon(Icons.location_on, color: const Color(0xFFD50000), size: 45),
                                  ),
                                  // Pin Head
                                  Container(
                                    width: 15,
                                    height: 15,
                                    margin: const EdgeInsets.only(top: 10),
                                    decoration: const BoxDecoration(
                                      color: Colors.amber,
                                      shape: BoxShape.circle,
                                    ),
                                  )
                                ],
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                  
                  const SizedBox(height: 40),
                  
                  // Spin Button
                  GestureDetector(
                    onTap: _spinWheel,
                    child: ScaleTransition(
                      scale: _pulseAnimation,
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 60, vertical: 15),
                        decoration: BoxDecoration(
                          gradient: const LinearGradient(
                            colors: [Color(0xFF64DD17), Color(0xFF33691E)], // Bright Green to Dark Green
                            begin: Alignment.topCenter,
                            end: Alignment.bottomCenter,
                          ),
                          borderRadius: BorderRadius.circular(50),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.greenAccent.withOpacity(0.6),
                              blurRadius: 20,
                              spreadRadius: 2,
                              offset: const Offset(0, 5),
                            ),
                            const BoxShadow(
                              color: Colors.black26,
                              offset: Offset(0, 5),
                              blurRadius: 5,
                            )
                          ],
                          border: Border.all(color: Colors.lightGreenAccent, width: 2),
                        ),
                        child: Text(
                          _isSpinning ? "SPINNING..." : "SPIN",
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 24,
                            fontWeight: FontWeight.bold,
                            letterSpacing: 1.5,
                            shadows: [Shadow(color: Colors.black45, offset: Offset(1, 1), blurRadius: 2)],
                          ),
                        ),
                      ),
                    ),
                  ),
                  
                  const SizedBox(height: 30),
                  
                  // Footer Info
                  Container(
                    margin: const EdgeInsets.symmetric(horizontal: 40),
                    padding: const EdgeInsets.symmetric(vertical: 8, horizontal: 20),
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: Colors.white24),
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Row(
                          children: [
                            const Icon(Icons.refresh, color: Colors.white70, size: 20),
                            const SizedBox(width: 8),
                            Text(
                              "${appProvider.luckyWheelLimit - appProvider.luckyWheelSpinsCount} Left",
                              style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
                            ),
                          ],
                        ),
                        Container(
                          key: _coinIconKey,
                          child: Row(
                            children: [
                              const Icon(Icons.monetization_on, color: Colors.amber, size: 20),
                              const SizedBox(width: 8),
                              AnimatedCoinBalance(
                                balance: appProvider.coins,
                                style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),
          
          // Close Button
          Positioned(
            top: 40,
            right: 20,
            child: GestureDetector(
              onTap: () => Navigator.pop(context),
              child: Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: Colors.black.withOpacity(0.5),
                  shape: BoxShape.circle,
                  border: Border.all(color: Colors.white24),
                ),
                child: const Icon(Icons.close, color: Colors.white, size: 24),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class ProfessionalWheelPainter extends CustomPainter {
  final List<int> rewards;
  final bool lightsOn;

  ProfessionalWheelPainter({required this.rewards, required this.lightsOn});

  @override
  void paint(Canvas canvas, Size size) {
    final center = Offset(size.width / 2, size.height / 2);
    final radius = size.width / 2;
    final rect = Rect.fromCircle(center: center, radius: radius);
    
    final paint = Paint()..style = PaintingStyle.fill;
    final textPainter = TextPainter(textDirection: TextDirection.ltr);
    
    // 1. Draw Outer Rim (Gold)
    final rimPaint = Paint()
      ..shader = const RadialGradient(
        colors: [Color(0xFFFFECB3), Color(0xFFFF6F00)],
        stops: [0.85, 1.0],
      ).createShader(rect);
    canvas.drawCircle(center, radius, rimPaint);
    
    // 2. Draw Inner Background (Dark)
    canvas.drawCircle(center, radius * 0.9, Paint()..color = const Color(0xFF1A237E));

    const double sliceAngle = 2 * math.pi / 8;
    final sliceRadius = radius * 0.85;
    final sliceRect = Rect.fromCircle(center: center, radius: sliceRadius);

    for (int i = 0; i < 8; i++) {
      // 3. Draw Slice
      // Alternating Colors: Light Blue & Dark Blue/Purple
      final isEven = i % 2 == 0;
      paint.color = isEven ? const Color(0xFF039BE5) : const Color(0xFF3949AB);
      
      // Add Gradient to slice for 3D effect
      paint.shader = RadialGradient(
        colors: isEven 
            ? [const Color(0xFF29B6F6), const Color(0xFF0277BD)] 
            : [const Color(0xFF5C6BC0), const Color(0xFF283593)],
        center: Alignment.center,
        radius: 0.8,
      ).createShader(sliceRect);

      // Start angle calculation:
      // We want index 0 to be centered at -pi/2 (Top).
      // So start angle for index 0 should be -pi/2 - (sliceAngle/2).
      double startAngle = -math.pi / 2 - (sliceAngle / 2) + (i * sliceAngle);
      
      canvas.drawArc(sliceRect, startAngle, sliceAngle, true, paint);
      
      // Draw Slice Border
      final borderPaint = Paint()
        ..style = PaintingStyle.stroke
        ..color = Colors.white.withOpacity(0.3)
        ..strokeWidth = 1;
      canvas.drawArc(sliceRect, startAngle, sliceAngle, true, borderPaint);
      
      // 4. Draw Content (Icon + Text)
      double midAngle = startAngle + (sliceAngle / 2);
      double contentRadius = sliceRadius * 0.65;
      
      final x = center.dx + contentRadius * math.cos(midAngle);
      final y = center.dy + contentRadius * math.sin(midAngle);
      
      canvas.save();
      canvas.translate(x, y);
      canvas.rotate(midAngle + math.pi / 2); // Rotate to face center
      
      // Draw Text
      textPainter.text = TextSpan(
        text: "${rewards[i]}",
        style: const TextStyle(
          color: Colors.white,
          fontSize: 20,
          fontWeight: FontWeight.bold,
          shadows: [Shadow(color: Colors.black, offset: Offset(1, 1), blurRadius: 2)],
        ),
      );
      textPainter.layout();
      textPainter.paint(canvas, Offset(-textPainter.width / 2, 0));
      
      // Draw Icon above text
      final icon = isEven ? Icons.monetization_on : Icons.diamond;
      final iconColor = isEven ? Colors.amber : Colors.pinkAccent;
      
      TextSpan iconSpan = TextSpan(
        text: String.fromCharCode(icon.codePoint),
        style: TextStyle(
          fontSize: 24,
          fontFamily: icon.fontFamily,
          color: iconColor,
          package: icon.fontPackage, // Crucial for standard icons
        ),
      );
      final iconPainter = TextPainter(
        text: iconSpan,
        textDirection: TextDirection.ltr,
      );
      iconPainter.layout();
      iconPainter.paint(canvas, Offset(-iconPainter.width / 2, -30));
      
      canvas.restore();
    }
    
    // 5. Draw Lights on Rim
    final lightRadius = radius * 0.95;
    final lightPaint = Paint()..style = PaintingStyle.fill;
    
    int totalLights = 24;
    double anglePerLight = 2 * math.pi / totalLights;
    
    for (int i = 0; i < totalLights; i++) {
      double angle = i * anglePerLight;
      double lx = center.dx + lightRadius * math.cos(angle);
      double ly = center.dy + lightRadius * math.sin(angle);
      
      // Blinking effect: Even lights on when lightsOn=true, Odd when lightsOn=false
      bool isOn = (i % 2 == 0) == lightsOn;
      
      lightPaint.color = isOn ? const Color(0xFFFFEB3B) : const Color(0xFF5D4037);
      
      canvas.drawCircle(Offset(lx, ly), 4, lightPaint);
      
      // Add Glow if on
      if (isOn) {
         canvas.drawCircle(Offset(lx, ly), 6, Paint()..color = Colors.yellow.withOpacity(0.5)..maskFilter = const MaskFilter.blur(BlurStyle.normal, 2));
      }
    }
  }

  @override
  bool shouldRepaint(covariant ProfessionalWheelPainter oldDelegate) {
    return oldDelegate.lightsOn != lightsOn || oldDelegate.rewards != rewards;
  }
}

class RibbonPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..style = PaintingStyle.fill
      ..shader = const LinearGradient(
        colors: [Color(0xFF7B1FA2), Color(0xFFBA68C8)],
        begin: Alignment.topCenter,
        end: Alignment.bottomCenter,
      ).createShader(Rect.fromLTWH(0, 0, size.width, size.height));

    final path = Path();
    
    // Main Ribbon Shape (Curved Banner)
    // Top Curve
    path.moveTo(0, 20);
    path.quadraticBezierTo(size.width / 2, -10, size.width, 20);
    
    // Right Side
    path.lineTo(size.width, size.height - 20);
    
    // Bottom Curve
    path.quadraticBezierTo(size.width / 2, size.height + 10, 0, size.height - 20);
    
    path.close();
    
    // Draw Shadow first
    canvas.drawShadow(path, Colors.black, 10, true);
    
    // Draw Ribbon
    canvas.drawPath(path, paint);
    
    // Add Glossy Highlight
    final highlightPaint = Paint()
      ..style = PaintingStyle.fill
      ..color = Colors.white.withOpacity(0.1);
      
    final highlightPath = Path();
    highlightPath.moveTo(10, 25);
    highlightPath.quadraticBezierTo(size.width / 2, 0, size.width - 10, 25);
    highlightPath.lineTo(size.width - 10, 45);
    highlightPath.quadraticBezierTo(size.width / 2, 20, 10, 45);
    highlightPath.close();
    
    canvas.drawPath(highlightPath, highlightPaint);
    
    // Border
    final borderPaint = Paint()
      ..style = PaintingStyle.stroke
      ..color = const Color(0xFFFFD54F) // Gold
      ..strokeWidth = 2;
      
    canvas.drawPath(path, borderPaint);
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}
