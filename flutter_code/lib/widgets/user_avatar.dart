import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter_animate/flutter_animate.dart';

class UserAvatar extends StatelessWidget {
  final String? imageUrl;
  final String userName;
  final double radius;
  final Color? backgroundColor;
  final Color? borderColor;
  final double borderWidth;
  final bool showBadge;

  const UserAvatar({
    super.key,
    this.imageUrl,
    this.userName = 'User',
    this.radius = 20,
    this.backgroundColor,
    this.borderColor,
    this.borderWidth = 0,
    this.showBadge = false,
  });

  @override
  Widget build(BuildContext context) {
    final initials = _getInitials(userName);
    final bgColor = backgroundColor ?? Colors.indigo.shade100;
    final textColor = Colors.indigo.shade800;

    return Container(
      width: radius * 2,
      height: radius * 2,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        border: borderWidth > 0
            ? Border.all(
                color: borderColor ?? Colors.white,
                width: borderWidth,
              )
            : null,
        boxShadow: borderWidth > 0
            ? [
                BoxShadow(
                  color: Colors.black.withOpacity(0.1),
                  blurRadius: 4,
                  offset: const Offset(0, 2),
                )
              ]
            : null,
      ),
      child: Stack(
        clipBehavior: Clip.none,
        children: [
          // Avatar Image or Initials
          ClipOval(
            child: SizedBox(
              width: radius * 2,
              height: radius * 2,
              child: (imageUrl != null && imageUrl!.isNotEmpty && !imageUrl!.contains('default.png'))
                  ? CachedNetworkImage(
                      imageUrl: imageUrl!,
                      fit: BoxFit.cover,
                      placeholder: (context, url) => Container(
                        color: bgColor,
                        child: Center(
                          child: CircularProgressIndicator(
                            strokeWidth: 2,
                            color: textColor.withOpacity(0.5),
                          ),
                        ),
                      ),
                      errorWidget: (context, url, error) => _buildInitials(initials, bgColor, textColor),
                    )
                  : _buildInitials(initials, bgColor, textColor),
            ),
          ),
          
          // Online/Verified Badge (Optional)
          if (showBadge)
            Positioned(
              right: 0,
              bottom: 0,
              child: Container(
                width: radius * 0.6,
                height: radius * 0.6,
                decoration: BoxDecoration(
                  color: Colors.green,
                  shape: BoxShape.circle,
                  border: Border.all(color: Colors.white, width: 2),
                ),
              ).animate().scale(duration: 300.ms, curve: Curves.elasticOut),
            ),
        ],
      ),
    );
  }

  Widget _buildInitials(String initials, Color bgColor, Color textColor) {
    return Container(
      color: bgColor,
      alignment: Alignment.center,
      child: Text(
        initials,
        style: TextStyle(
          color: textColor,
          fontWeight: FontWeight.bold,
          fontSize: radius * 0.8,
        ),
      ),
    );
  }

  String _getInitials(String name) {
    if (name.isEmpty) return 'U';
    final parts = name.trim().split(' ');
    if (parts.length >= 2) {
      return '${parts[0][0]}${parts[1][0]}'.toUpperCase();
    }
    return name[0].toUpperCase();
  }
}
