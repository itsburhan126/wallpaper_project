import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/app_provider.dart';
import '../providers/language_provider.dart';
import '../models/wallpaper_model.dart';
import '../services/api_service.dart';
import '../widgets/wallpaper_card.dart';
import '../widgets/shimmer_loading.dart';
import '../screens/wallpaper_detail_screen.dart';
import '../providers/ad_provider.dart';
import '../widgets/ads/universal_native_ad.dart';

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

  Widget _buildWallpaperRow(BuildContext context, int startIndex, int endIndex, int cols) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      child: LayoutBuilder(
        builder: (context, constraints) {
          double totalWidth = constraints.maxWidth;
          double spacing = 10;
          double itemWidth = (totalWidth - (spacing * (cols - 1))) / cols;
          double itemHeight = itemWidth / 0.55;
          
          List<Widget> children = [];
          for (int i = startIndex; i < endIndex; i++) {
             final wallpaper = _wallpapers[i];
             final String heroTag = '${wallpaper.url}_${widget.identifier ?? "tab"}_${widget.categoryId ?? "home"}_${widget.isHot}_$i';
             
             children.add(
               SizedBox(
                 width: itemWidth,
                 height: itemHeight,
                 child: WallpaperCard(
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
                 ),
               )
             );
             
             if (i < endIndex - 1) {
               children.add(SizedBox(width: spacing));
             }
          }
          
          // Fill remaining space if row is incomplete
          if (endIndex - startIndex < cols) {
             int missing = cols - (endIndex - startIndex);
             for (int k = 0; k < missing; k++) {
                children.add(SizedBox(width: spacing));
                children.add(SizedBox(width: itemWidth, height: itemHeight)); // Empty placeholder
             }
          }
          
          return Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: children,
          );
        },
      ),
    );
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
                    Provider.of<LanguageProvider>(context).getText('failed_load_wallpapers'),
                    style: TextStyle(color: Colors.white.withOpacity(0.7)),
                  ),
                  TextButton(
                    onPressed: () => _fetchData(),
                    child: Text(Provider.of<LanguageProvider>(context).getText('retry')),
                  ),
                ],
              ),
            )
          else if (_wallpapers.isEmpty)
            SliverFillRemaining(
              child: Center(
                child: Text(
                  Provider.of<LanguageProvider>(context).getText('no_wallpapers_found'),
                  style: TextStyle(color: Colors.white.withOpacity(0.5)),
                ),
              ),
            )
          else
            SliverPadding(
              padding: const EdgeInsets.all(12),
              sliver: SliverList(
                delegate: SliverChildBuilderDelegate(
                  (context, index) {
                    final adProvider = Provider.of<AdProvider>(context);
                    final String screenName = widget.categoryId == null ? 'home' : 'category';
                    final bool showAds = adProvider.nativeEnabled && adProvider.getNativeNetworkForScreen(screenName) != 'none';
                    final int adInterval = adProvider.homeAdInterval;
                    final int cols = 3;

                    if (!showAds) {
                       int startIndex = index * cols;
                       int endIndex = (startIndex + cols) > _wallpapers.length ? _wallpapers.length : (startIndex + cols);
                       if (startIndex >= _wallpapers.length) return const SizedBox.shrink();
                       
                       return _buildWallpaperRow(context, startIndex, endIndex, cols);
                    }

                    int rowsBeforeAd = (adInterval / cols).ceil();
                    int rowsPerBlock = rowsBeforeAd + 1;
                    
                    int blockIndex = index ~/ rowsPerBlock;
                    int rowIndexInBlock = index % rowsPerBlock;
                    
                    if (rowIndexInBlock == rowsBeforeAd) {
                      return UniversalNativeAd(screen: screenName);
                    }
                    
                    int baseIndex = blockIndex * adInterval;
                    int offsetInBlock = rowIndexInBlock * cols;
                    int startIndex = baseIndex + offsetInBlock;
                    
                    int maxIndexForBlock = baseIndex + adInterval;
                    int endIndex = (startIndex + cols) > _wallpapers.length ? _wallpapers.length : (startIndex + cols);
                    if (endIndex > maxIndexForBlock) endIndex = maxIndexForBlock;
                    
                    if (startIndex >= endIndex) return const SizedBox.shrink();
                    
                    return _buildWallpaperRow(context, startIndex, endIndex, cols);
                  },
                  childCount: () {
                    final adProvider = Provider.of<AdProvider>(context);
                    final String screenName = widget.categoryId == null ? 'home' : 'category';
                    final bool showAds = adProvider.nativeEnabled && adProvider.getNativeNetworkForScreen(screenName) != 'none';
                    final int adInterval = adProvider.homeAdInterval;
                    final int cols = 3;
                    
                    if (!showAds) {
                      return (_wallpapers.length / cols).ceil();
                    }
                    
                    int fullBlocks = _wallpapers.length ~/ adInterval;
                    int remaining = _wallpapers.length % adInterval;
                    int rowsBeforeAd = (adInterval / cols).ceil();
                    int rowsPerBlock = rowsBeforeAd + 1;
                    
                    int count = fullBlocks * rowsPerBlock;
                    if (remaining > 0) {
                      count += (remaining / cols).ceil();
                    }
                    return count;
                  }(),
                ),
              ),
            ),
        ],
      ),
    );
  }
}
