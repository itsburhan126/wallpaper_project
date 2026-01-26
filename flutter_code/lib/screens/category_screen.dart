import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:flutter_animate/flutter_animate.dart';
import '../providers/app_provider.dart';
import '../providers/language_provider.dart';
import '../providers/ad_provider.dart';
import '../services/ad_manager_service.dart';
import '../widgets/shimmer_loading.dart';
import '../widgets/wallpaper_tab.dart';
import '../models/category_model.dart';
import '../utils/app_theme.dart';

class CategoryScreen extends StatelessWidget {
  const CategoryScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.darkBackgroundColor, // Ultra dark background
      bottomNavigationBar: null,
      body: Consumer2<AppProvider, LanguageProvider>(
        builder: (context, provider, langProvider, _) {
          return RefreshIndicator(
            backgroundColor: const Color(0xFF2A1B4E),
            color: Colors.white,
            displacement: 40,
            strokeWidth: 3,
            onRefresh: () async {
              await provider.fetchCategories();
            },
            child: CustomScrollView(
              physics: const BouncingScrollPhysics(parent: AlwaysScrollableScrollPhysics()),
              slivers: [
                // Premium Large Header
              SliverAppBar(
                backgroundColor: AppTheme.darkBackgroundColor,
                expandedHeight: 160.0,
                floating: false,
                pinned: true,
                elevation: 0,
                flexibleSpace: FlexibleSpaceBar(
                  centerTitle: false,
                  titlePadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
                  title: Column(
                    mainAxisSize: MainAxisSize.min,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisAlignment: MainAxisAlignment.end,
                    children: [
                      Text(
                        langProvider.getText('collections'),
                        style: GoogleFonts.poppins(
                          fontWeight: FontWeight.w700,
                          color: Colors.white,
                          fontSize: 28,
                          height: 1.0,
                        ),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        langProvider.getText('curated_for_you'),
                        style: GoogleFonts.poppins(
                          fontWeight: FontWeight.w400,
                          color: Colors.white54,
                          fontSize: 10,
                          letterSpacing: 1.2,
                        ),
                      ),
                    ],
                  ),
                  background: Container(
                    decoration: AppTheme.backgroundDecoration,
                  ),
                ),
              ),

              // Content
              if (provider.isLoading && provider.categories.isEmpty)
                SliverPadding(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
                  sliver: SliverGrid(
                    gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                      crossAxisCount: 2,
                      childAspectRatio: 0.75,
                      crossAxisSpacing: 16,
                      mainAxisSpacing: 16,
                    ),
                    delegate: SliverChildBuilderDelegate(
                      (context, index) => const ShimmerLoading.rectangular(
                        height: double.infinity,
                        shapeBorder: RoundedRectangleBorder(
                          borderRadius: BorderRadius.all(Radius.circular(24)),
                        ),
                      ),
                      childCount: 6,
                    ),
                  ),
                )
              else if (provider.categories.isEmpty)
                SliverFillRemaining(
                  child: Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Container(
                          padding: const EdgeInsets.all(24),
                          decoration: BoxDecoration(
                            color: Colors.white.withValues(alpha: 0.05),
                            shape: BoxShape.circle,
                          ),
                          child: Icon(Icons.grid_view_rounded, size: 60, color: Colors.white.withValues(alpha: 0.2)),
                        ),
                        const SizedBox(height: 24),
                        Text(
                          langProvider.getText('no_collections_found'),
                          style: GoogleFonts.poppins(color: Colors.white54, fontSize: 16),
                        ),
                      ],
                    ),
                  ),
                )
              else
                SliverPadding(
                  padding: const EdgeInsets.only(left: 16, right: 16, top: 8, bottom: 100),
                  sliver: SliverGrid(
                    gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                      crossAxisCount: 2,
                      childAspectRatio: 0.75, // Modern aspect ratio
                      crossAxisSpacing: 16,
                      mainAxisSpacing: 16,
                    ),
                    delegate: SliverChildBuilderDelegate(
                      (context, index) {
                        final category = provider.categories[index];
                        return _buildUltraCategoryCard(context, category, index)
                            .animate(delay: (50 * index).ms)
                            .fadeIn(duration: 500.ms)
                            .moveY(begin: 30, end: 0, curve: Curves.easeOutCubic);
                      },
                      childCount: provider.categories.length,
                    ),
                  ),
                ),
            ],
          ),
          );
        },
      ),
    );
  }

  Widget _buildUltraCategoryCard(BuildContext context, Category category, int index) {
    return Container(
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(24),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.5),
            blurRadius: 20,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(24),
        child: Stack(
          fit: StackFit.expand,
          children: [
            // 1. Background Image with Zoom capability (static for now, dynamic if we had state)
            if (category.coverUrl.isNotEmpty)
              CachedNetworkImage(
                imageUrl: category.coverUrl,
                fit: BoxFit.cover,
                width: double.infinity,
                height: double.infinity,
                memCacheHeight: 600,
                placeholder: (context, url) => Container(
                  color: const Color(0xFF1A1A1A),
                  child: Center(
                    child: CircularProgressIndicator(
                      strokeWidth: 2,
                      color: Colors.white.withValues(alpha: 0.1),
                    ),
                  ),
                ),
                errorWidget: (context, url, error) => Container(
                  color: const Color(0xFF1A1A1A),
                  child: const Icon(Icons.broken_image_outlined, color: Colors.white24),
                ),
              )
            else
              Container(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: [
                      Colors.primaries[index % Colors.primaries.length].shade900,
                      Colors.black,
                    ],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                ),
                child: const Icon(Icons.category_rounded, color: Colors.white10, size: 48),
              ),

            // 2. Subtle Dark Gradient at bottom for text readability
            Positioned(
              left: 0,
              right: 0,
              bottom: 0,
              height: 120,
              child: Container(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topCenter,
                    end: Alignment.bottomCenter,
                    colors: [
                      Colors.transparent,
                      Colors.black.withValues(alpha: 0.8),
                    ],
                  ),
                ),
              ),
            ),

            // 3. Glassmorphism Label Container
            Positioned(
              bottom: 12,
              left: 12,
              right: 12,
              child: ClipRRect(
                borderRadius: BorderRadius.circular(16),
                child: BackdropFilter(
                  filter: ImageFilter.blur(sigmaX: 10.0, sigmaY: 10.0),
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                    decoration: BoxDecoration(
                      color: Colors.black.withValues(alpha: 0.4),
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(
                        color: Colors.white.withValues(alpha: 0.15),
                        width: 1,
                      ),
                    ),
                    child: Row(
                      children: [
                        Expanded(
                          child: Text(
                            category.name,
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: GoogleFonts.poppins(
                              color: Colors.white,
                              fontSize: 14,
                              fontWeight: FontWeight.w600,
                              letterSpacing: 0.5,
                            ),
                          ),
                        ),
                        Container(
                          padding: const EdgeInsets.all(4),
                          decoration: BoxDecoration(
                            color: Colors.white.withValues(alpha: 0.1),
                            shape: BoxShape.circle,
                          ),
                          child: const Icon(
                            Icons.arrow_forward_rounded,
                            color: Colors.white,
                            size: 12,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),

            // 4. Touch Ripple Overlay
            Positioned.fill(
              child: Material(
                color: Colors.transparent,
                child: InkWell(
                  onTap: () async {
                    // Show Ad before navigation
                    final adProvider = Provider.of<AdProvider>(context, listen: false);
                    if (adProvider.adsEnabled) {
                       await AdManager.showAdWithFallback(
                         context, 
                         adProvider.adPriorities, 
                         () {}
                       );
                    }

                    if (context.mounted) {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => Scaffold(
                            backgroundColor: const Color(0xFF050505),
                            appBar: AppBar(
                              backgroundColor: const Color(0xFF050505),
                              iconTheme: const IconThemeData(color: Colors.white),
                              elevation: 0,
                              centerTitle: true,
                              title: Text(
                                category.name,
                                style: GoogleFonts.poppins(
                                  fontWeight: FontWeight.bold,
                                  color: Colors.white,
                                ),
                              ),
                            ),
                            body: WallpaperTab(
                              categoryId: category.id,
                              withSliverOverlap: false,
                            ),
                          ),
                        ),
                      );
                    }
                  },
                  splashColor: Colors.white.withValues(alpha: 0.1),
                  highlightColor: Colors.white.withValues(alpha: 0.05),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
