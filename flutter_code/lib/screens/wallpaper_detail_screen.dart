import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:provider/provider.dart';
import 'package:glassmorphism/glassmorphism.dart';
import 'package:flutter_animate/flutter_animate.dart';
import '../models/wallpaper_model.dart';
import '../providers/app_provider.dart';
import '../utils/constants.dart';
import '../services/mock_ad_service.dart';

class WallpaperDetailScreen extends StatefulWidget {
  final Wallpaper wallpaper;

  const WallpaperDetailScreen({super.key, required this.wallpaper});

  @override
  State<WallpaperDetailScreen> createState() => _WallpaperDetailScreenState();
}

class _WallpaperDetailScreenState extends State<WallpaperDetailScreen> {
  bool _showEarnButton = false;

  @override
  void initState() {
    super.initState();
    // Show earn button after 2 seconds of viewing
    Future.delayed(const Duration(seconds: 2), () {
      if (mounted) {
        setState(() {
          _showEarnButton = true;
        });
      }
    });
  }

  void _showCoinDialog() {
    showDialog(
      context: context,
      builder: (context) => Dialog(
        backgroundColor: Colors.transparent,
        child: GlassmorphicContainer(
          width: 300,
          height: 250,
          borderRadius: 20,
          blur: 20,
          alignment: Alignment.center,
          border: 2,
          linearGradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [
              Colors.white.withOpacity(0.1),
              Colors.white.withOpacity(0.05),
            ],
          ),
          borderGradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [
              Colors.white.withOpacity(0.5),
              Colors.white.withOpacity(0.1),
            ],
          ),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.monetization_on, color: Colors.amber, size: 50),
              const SizedBox(height: 20),
              Text(
                "Earn Coins!",
                style: AppTextStyles.header,
              ),
              const SizedBox(height: 10),
              Text(
                "Watch an ad to earn 10 coins",
                style: AppTextStyles.body,
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 20),
              ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppColors.accent,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(30)),
                ),
                onPressed: () {
                  Navigator.pop(context); // Close dialog
                  _watchAd();
                },
                child: const Text("Watch Ad", style: TextStyle(color: Colors.white)),
              ),
            ],
          ),
        ),
      ),
    );
  }

  void _watchAd() {
    MockAdService.showRewardedAd(context, () {
      // On Ad Closed & Rewarded
      Provider.of<AppProvider>(context, listen: false).addCoins(10);
      _showCongratulationDialog();
    });
  }

  void _showCongratulationDialog() {
    showDialog(
      context: context,
      builder: (context) => Dialog(
        backgroundColor: Colors.transparent,
        child: GlassmorphicContainer(
          width: 300,
          height: 250,
          borderRadius: 20,
          blur: 20,
          alignment: Alignment.center,
          border: 2,
          linearGradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [Colors.green.withOpacity(0.2), Colors.green.withOpacity(0.1)]),
          borderGradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [Colors.green.withOpacity(0.5), Colors.green.withOpacity(0.1)]),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.check_circle, color: Colors.greenAccent, size: 60)
                  .animate()
                  .scale(duration: 500.ms, curve: Curves.elasticOut),
              const SizedBox(height: 20),
              Text("Congratulations!", style: AppTextStyles.header),
              const SizedBox(height: 10),
              Text("You earned 10 Coins!", style: AppTextStyles.body),
              const SizedBox(height: 20),
              ElevatedButton(
                onPressed: () => Navigator.pop(context),
                child: const Text("Awesome"),
              )
            ],
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios, color: Colors.white),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: Stack(
        children: [
          // Full Screen Image
          Positioned.fill(
            child: CachedNetworkImage(
              imageUrl: widget.wallpaper.url,
              fit: BoxFit.cover,
              placeholder: (context, url) => const Center(child: CircularProgressIndicator()),
              errorWidget: (context, url, error) => const Icon(Icons.error),
            ),
          ),
          
          // Bottom Actions
          Positioned(
            bottom: 40,
            left: 20,
            right: 20,
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              children: [
                _buildActionButton(Icons.download, "Download", () {
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(content: Text("Downloading wallpaper... (Simulated)")),
                  );
                }),
                _buildActionButton(Icons.wallpaper, "Apply", () {
                   ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(content: Text("Applying wallpaper... (Simulated)")),
                  );
                }),
              ],
            ),
          ),

          // Floating Coin Button
          if (_showEarnButton)
            Positioned(
              top: 100,
              right: 20,
              child: GestureDetector(
                onTap: _showCoinDialog,
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  decoration: BoxDecoration(
                    color: Colors.amber,
                    borderRadius: BorderRadius.circular(30),
                    boxShadow: [
                      BoxShadow(color: Colors.amber.withOpacity(0.5), blurRadius: 10, spreadRadius: 2)
                    ],
                  ),
                  child: Row(
                    children: const [
                      Icon(Icons.monetization_on, color: Colors.white),
                      SizedBox(width: 5),
                      Text("Get Coins", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                    ],
                  ),
                ).animate().fade().slideX(begin: 1.0, end: 0.0),
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildActionButton(IconData icon, String label, VoidCallback onTap) {
    return GlassmorphicContainer(
      width: 120,
      height: 50,
      borderRadius: 25,
      blur: 10,
      alignment: Alignment.center,
      border: 1,
      linearGradient: LinearGradient(
        colors: [Colors.black.withOpacity(0.5), Colors.black.withOpacity(0.3)],
      ),
      borderGradient: LinearGradient(
        colors: [Colors.white.withOpacity(0.2), Colors.white.withOpacity(0.1)],
      ),
      child: InkWell(
        onTap: onTap,
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon, color: Colors.white),
            const SizedBox(width: 8),
            Text(label, style: const TextStyle(color: Colors.white)),
          ],
        ),
      ),
    );
  }
}
