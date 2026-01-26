import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:carousel_slider/carousel_slider.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:url_launcher/url_launcher.dart';
import '../providers/app_provider.dart';
import '../providers/language_provider.dart';
import '../utils/constants.dart';
import '../widgets/wallpaper_tab.dart';
import '../widgets/shimmer_loading.dart';
import '../models/category_model.dart';
import 'shorts_screen.dart';
import 'task_screen.dart';
import 'all_games_screen.dart';
import '../utils/app_theme.dart';
import '../widgets/toast/professional_toast.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  int _currentBannerIndex = 0;

  @override
  Widget build(BuildContext context) {
    final languageProvider = Provider.of<LanguageProvider>(context);
    
    final List<String> fixedTabs = [
      languageProvider.getText('tab_hot'),
      languageProvider.getText('tab_new'),
      languageProvider.getText('tab_rankings')
    ];

    return Consumer<AppProvider>(
      builder: (context, provider, _) {
        // Construct the full list of tabs: Fixed + Categories
        final List<String> tabs = [...fixedTabs];
        
        if (provider.categories.isNotEmpty) {
          tabs.addAll(provider.categories.map((c) => c.name));
        }

        return DefaultTabController(
          length: tabs.length,
          child: Scaffold(
            backgroundColor: AppTheme.darkBackgroundColor,
            bottomNavigationBar: null,
            body: NestedScrollView(
              headerSliverBuilder: (context, innerBoxIsScrolled) {
                  return [
                    SliverOverlapAbsorber(
                      handle: NestedScrollView.sliverOverlapAbsorberHandleFor(context),
                      sliver: SliverAppBar(
                        backgroundColor: AppTheme.darkBackgroundColor,
                        floating: true,
                        pinned: true,
                        snap: true,
                        elevation: 0,
                        flexibleSpace: FlexibleSpaceBar(
                          background: Container(
                            decoration: AppTheme.backgroundDecoration,
                          ),
                        ),
                        titleSpacing: 0,
                        toolbarHeight: 70,
                        title: Padding(
                          padding: const EdgeInsets.symmetric(horizontal: 16.0),
                          child: Row(
                            children: [
                              Expanded(
                                child: Container(
                                  height: 45,
                                  decoration: BoxDecoration(
                                    color: Colors.white.withOpacity(0.1),
                                    borderRadius: BorderRadius.circular(25),
                                  ),
                                  padding: const EdgeInsets.symmetric(horizontal: 15),
                                  child: Row(
                                    children: [
                                      Icon(Icons.search, color: Colors.white.withOpacity(0.5), size: 24),
                                      const SizedBox(width: 10),
                                      Expanded(
                                        child: Text(
                                          languageProvider.getText('search_hint'),
                                          style: GoogleFonts.poppins(
                                            color: Colors.white.withOpacity(0.7),
                                            fontSize: 14,
                                          ),
                                          overflow: TextOverflow.ellipsis,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                              const SizedBox(width: 15),
                              GestureDetector(
                                onTap: () {
                                  Navigator.push(
                                    context,
                                    MaterialPageRoute(builder: (context) => const ShortsScreen()),
                                  );
                                },
                                child: Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                                  decoration: BoxDecoration(
                                    gradient: const LinearGradient(
                                      colors: [Colors.purple, Colors.deepPurple],
                                    ),
                                    borderRadius: BorderRadius.circular(20),
                                  ),
                                  child: Row(
                                    children: [
                                      const Icon(Icons.play_circle_fill, color: Colors.white, size: 16),
                                      const SizedBox(width: 4),
                                      Text(
                                        languageProvider.getText('nav_shorts'),
                                        style: GoogleFonts.poppins(
                                          color: Colors.white,
                                          fontWeight: FontWeight.bold,
                                          fontSize: 12,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                              const SizedBox(width: 15),
                              GestureDetector(
                                onTap: () {
                                  Navigator.push(
                                    context,
                                    MaterialPageRoute(builder: (context) => const TaskScreen()),
                                  );
                                },
                                child: const Icon(FontAwesomeIcons.gift, color: Colors.orange, size: 22),
                              ),
                              const SizedBox(width: 15),
                              GestureDetector(
                                onTap: () {
                                  Navigator.push(
                                    context,
                                    MaterialPageRoute(builder: (context) => const AllGamesScreen()),
                                  );
                                },
                                child: Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                                  decoration: BoxDecoration(
                                    gradient: const LinearGradient(
                                      colors: [Colors.blue, Colors.blueAccent],
                                    ),
                                    borderRadius: BorderRadius.circular(20),
                                  ),
                                  child: Row(
                                    children: [
                                      const Icon(FontAwesomeIcons.gamepad, color: Colors.white, size: 14),
                                      const SizedBox(width: 4),
                                      Text(
                                        languageProvider.getText('play_earn'),
                                        style: GoogleFonts.poppins(
                                          color: Colors.white,
                                          fontWeight: FontWeight.bold,
                                          fontSize: 10,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                        bottom: PreferredSize(
                          preferredSize: const Size.fromHeight(50),
                          child: Container(
                            height: 50,
                            alignment: Alignment.centerLeft,
                            child: TabBar(
                              isScrollable: true,
                              indicatorColor: Colors.transparent,
                              dividerColor: Colors.transparent,
                              labelColor: Colors.white,
                              unselectedLabelColor: Colors.white54,
                              labelStyle: GoogleFonts.poppins(fontSize: 18, fontWeight: FontWeight.bold),
                              unselectedLabelStyle: GoogleFonts.poppins(fontSize: 16, fontWeight: FontWeight.w500),
                              padding: const EdgeInsets.symmetric(horizontal: 10),
                              tabs: tabs.map((tab) => Tab(
                                child: Row(
                                  children: [
                                    Text(tab),
                                    if (tab == "Hot") ...[
                                      const SizedBox(width: 5),
                                      const Icon(Icons.local_fire_department, color: Colors.orange, size: 18),
                                    ]
                                  ],
                                ),
                              )).toList(),
                            ),
                          ),
                        ),
                      ),
                    ),
                  ];
                },
                body: TabBarView(
                  children: tabs.map((tabName) {
                    // 1. Hot Tab (with Banner)
                    if (tabName == languageProvider.getText('tab_hot')) {
                      return WallpaperTab(
                        isHot: false,
                        header: _buildBanner(provider),
                        identifier: "Hot",
                      );
                    }
                    
                    // 2. New Tab
                    if (tabName == languageProvider.getText('tab_new')) {
                      return WallpaperTab(isHot: false, identifier: "New");
                    }

                    // 3. Rankings Tab
                    if (tabName == languageProvider.getText('tab_rankings')) {
                       return WallpaperTab(isHot: false, identifier: "Rankings");
                    }

                    // 4. Category Tabs
                    final category = provider.categories.firstWhere(
                      (c) => c.name.toLowerCase() == tabName.toLowerCase(),
                      orElse: () => Category(id: '', name: '', coverUrl: ''),
                    );

                    if (category.id.isNotEmpty) {
                      return WallpaperTab(categoryId: category.id, identifier: tabName);
                    }

                    // 5. Fallback
                    return WallpaperTab(isHot: false, identifier: tabName); 
                  }).toList(),
                ),
              ),
            ),
        );
      },
    );
  }

  Widget _buildBanner(AppProvider provider) {
    final languageProvider = Provider.of<LanguageProvider>(context);
    if (provider.isLoading && provider.banners.isEmpty) {
      return const BannerShimmer();
    }
    if (provider.banners.isEmpty) return const SizedBox.shrink();

    return Padding(
      padding: const EdgeInsets.only(top: 10, bottom: 20),
      child: Column(
        children: [
          CarouselSlider(
            options: CarouselOptions(
              height: 220,
              viewportFraction: 0.92,
              enlargeCenterPage: true,
              autoPlay: true,
              autoPlayCurve: Curves.fastOutSlowIn,
              onPageChanged: (index, reason) {
                setState(() {
                  _currentBannerIndex = index;
                });
              },
            ),
            items: provider.banners.map((banner) {
              return Builder(
                builder: (BuildContext context) {
                  return GestureDetector(
                    onTap: () async {
                      if (banner.linkUrl != null && banner.linkUrl!.isNotEmpty) {
                        final Uri url = Uri.parse(banner.linkUrl!);
                        if (!await launchUrl(url, mode: LaunchMode.externalApplication)) {
                          if (context.mounted) {
                            ProfessionalToast.showError(context, message: "Could not launch ${banner.linkUrl}");
                          }
                        }
                      }
                    },
                    child: ClipRRect(
                      borderRadius: BorderRadius.circular(15),
                      child: Stack(
                        fit: StackFit.expand,
                        children: [
                          CachedNetworkImage(
                            imageUrl: banner.imageUrl,
                            fit: BoxFit.cover,
                            placeholder: (context, url) => Container(
                              color: Colors.grey[900],
                              child: const Center(child: CircularProgressIndicator(strokeWidth: 2)),
                            ),
                            errorWidget: (context, url, error) => Container(
                              color: Colors.grey[900],
                              child: const Icon(Icons.error, color: Colors.white54),
                            ),
                          ),
                          // Gradient Overlay
                          if (banner.title != null && banner.title!.isNotEmpty)
                          Container(
                            decoration: BoxDecoration(
                              gradient: LinearGradient(
                                begin: Alignment.topCenter,
                                end: Alignment.bottomCenter,
                                colors: [
                                  Colors.transparent,
                                  Colors.black.withOpacity(0.8),
                                ],
                              ),
                            ),
                          ),
                          // Text Content
                          if (banner.title != null && banner.title!.isNotEmpty)
                          Positioned(
                            bottom: 15,
                            left: 15,
                            right: 15,
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  banner.title!,
                                  style: GoogleFonts.blackOpsOne(
                                    color: const Color(0xFFFFD700), // Gold
                                    fontSize: 24,
                                    letterSpacing: 1.2,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                  );
                },
              );
            }).toList(),
          ),
          const SizedBox(height: 15),
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: provider.banners.asMap().entries.map((entry) {
              return AnimatedContainer(
                duration: const Duration(milliseconds: 300),
                width: _currentBannerIndex == entry.key ? 24.0 : 8.0,
                height: 8.0,
                margin: const EdgeInsets.symmetric(horizontal: 4.0),
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(4.0),
                  color: _currentBannerIndex == entry.key
                      ? const Color(0xFFFFD700) // Gold
                      : Colors.white.withOpacity(0.2),
                ),
              );
            }).toList(),
          ),
        ],
      ),
    );
  }
}
