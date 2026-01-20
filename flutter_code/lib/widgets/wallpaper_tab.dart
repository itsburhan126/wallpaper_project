import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/app_provider.dart';
import '../models/wallpaper_model.dart';
import '../services/api_service.dart';
import '../widgets/wallpaper_card.dart';
import '../widgets/shimmer_loading.dart';
import '../screens/wallpaper_detail_screen.dart';

class WallpaperTab extends StatefulWidget {
  final String? categoryId;
  final bool isHot;
  final String? scrollKey;
  final Widget? header;
  final String? identifier;
  final bool withSliverOverlap;

  const WallpaperTab({
    super.key,
    this.categoryId,
    this.isHot = false,
    this.scrollKey,
    this.header,
    this.identifier,
    this.withSliverOverlap = true,
  });

  @override
  State<WallpaperTab> createState() => _WallpaperTabState();
}

class _WallpaperTabState extends State<WallpaperTab> with AutomaticKeepAliveClientMixin {
  final ApiService _apiService = ApiService();
  List<Wallpaper> _wallpapers = [];
  bool _isLoading = true;
  String? _error;

  @override
  bool get wantKeepAlive => true;

  @override
  void initState() {
    super.initState();
    _fetchData();
  }

  Future<void> _fetchData({bool refresh = false}) async {
    if (!mounted) return;
    if (!refresh) {
      setState(() {
        _isLoading = true;
        _error = null;
      });
    } else {
      // Smart refresh: If on home tabs (no categoryId), refresh banners too
      if (widget.categoryId == null) {
        Provider.of<AppProvider>(context, listen: false).fetchBanners();
      }
    }

    try {
      final wallpapers = await _apiService.getWallpapers(
        categoryId: widget.categoryId,
        isHot: widget.isHot,
      );
      if (mounted) {
        setState(() {
          _wallpapers = wallpapers;
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _error = e.toString();
          _isLoading = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    super.build(context); // Required for AutomaticKeepAliveClientMixin

    return RefreshIndicator(
      backgroundColor: const Color(0xFF1E1E1E),
      color: Colors.white,
      displacement: 40,
      strokeWidth: 3,
      onRefresh: () => _fetchData(refresh: true),
      child: CustomScrollView(
        key: PageStorageKey<String>(widget.scrollKey ?? 'tab_${widget.categoryId}_${widget.isHot}'),
        physics: const BouncingScrollPhysics(parent: AlwaysScrollableScrollPhysics()),
        slivers: [
          if (widget.withSliverOverlap)
            SliverOverlapInjector(
              handle: NestedScrollView.sliverOverlapAbsorberHandleFor(context),
            ),
          if (widget.header != null) SliverToBoxAdapter(child: widget.header),
          
          if (_isLoading)
            const WallpaperShimmerGrid(isSliver: true)
          else if (_error != null)
            SliverFillRemaining(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(Icons.error_outline, color: Colors.red, size: 48),
                  const SizedBox(height: 16),
                  Text(
                    "Failed to load wallpapers",
                    style: TextStyle(color: Colors.white.withOpacity(0.7)),
                  ),
                  TextButton(
                    onPressed: () => _fetchData(),
                    child: const Text("Retry"),
                  ),
                ],
              ),
            )
          else if (_wallpapers.isEmpty)
            SliverFillRemaining(
              child: Center(
                child: Text(
                  "No wallpapers found",
                  style: TextStyle(color: Colors.white.withOpacity(0.5)),
                ),
              ),
            )
          else
            SliverPadding(
              padding: const EdgeInsets.all(12),
              sliver: SliverGrid(
                gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                  crossAxisCount: 3,
                  childAspectRatio: 0.55,
                  crossAxisSpacing: 10,
                  mainAxisSpacing: 10,
                ),
                delegate: SliverChildBuilderDelegate(
                  (context, index) {
                    final wallpaper = _wallpapers[index];
                    // Create a unique hero tag based on the tab context and index to ensure uniqueness even if wallpaper is duplicated
                    final String heroTag = '${wallpaper.url}_${widget.identifier ?? "tab"}_${widget.categoryId ?? "home"}_${widget.isHot}_$index';
                    
                    return WallpaperCard(
                      wallpaper: wallpaper,
                      heroTag: heroTag,
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => WallpaperDetailScreen(
                              wallpaper: wallpaper,
                              heroTag: heroTag,
                            ),
                          ),
                        );
                      },
                    );
                  },
                  childCount: _wallpapers.length,
                ),
              ),
            ),
        ],
      ),
    );
  }
}
