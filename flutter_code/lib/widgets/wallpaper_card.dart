import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter_animate/flutter_animate.dart';
import '../models/wallpaper_model.dart';

class WallpaperCard extends StatelessWidget {
  final Wallpaper wallpaper;
  final VoidCallback onTap;

  const WallpaperCard({
    super.key,
    required this.wallpaper,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Hero(
        tag: wallpaper.url, // Assuming url is unique enough for this context
        child: Container(
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(16),
            color: Colors.grey[900],
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.3),
                blurRadius: 8,
                offset: const Offset(0, 4),
              ),
            ],
          ),
          clipBehavior: Clip.antiAlias,
          child: Stack(
            fit: StackFit.expand,
            children: [
              CachedNetworkImage(
                imageUrl: wallpaper.thumbUrl,
                fit: BoxFit.cover,
                placeholder: (context, url) => Container(
                  color: Colors.grey[850],
                  child: const Center(
                    child: CircularProgressIndicator(
                      strokeWidth: 2, 
                      color: Colors.white24,
                    ),
                  ),
                ).animate(onPlay: (controller) => controller.repeat())
                 .shimmer(duration: 1200.ms, color: Colors.white10),
                errorWidget: (context, url, error) => const Icon(Icons.error),
              ),
              
              // Gradient Overlay for depth
              Container(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topCenter,
                    end: Alignment.bottomCenter,
                    colors: [
                      Colors.transparent,
                      Colors.black.withOpacity(0.0),
                      Colors.black.withOpacity(0.3),
                    ],
                    stops: const [0.0, 0.7, 1.0],
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
