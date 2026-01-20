import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:carousel_slider/carousel_slider.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:glassmorphism/glassmorphism.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import '../providers/app_provider.dart';
import '../utils/constants.dart';
import 'wallpaper_detail_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  final List<String> _tabs = ["Hot", "New", "Rankings", "Originals", "Animation", "Romance", "Action"];

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: _tabs.length, vsync: this);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.black,
      body: SafeArea(
        child: NestedScrollView(
          headerSliverBuilder: (context, innerBoxIsScrolled) {
            return [
              SliverAppBar(
                backgroundColor: Colors.black,
                floating: true,
                pinned: true,
                snap: true,
                elevation: 0,
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
                                  "The Wedding That Will Never Be",
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
                      const Icon(FontAwesomeIcons.gift, color: Colors.orange, size: 22),
                      const SizedBox(width: 15),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                        decoration: BoxDecoration(
                          color: const Color(0xFFFDD835),
                          borderRadius: BorderRadius.circular(5),
                        ),
                        child: Text(
                          "VIP",
                          style: GoogleFonts.poppins(
                            color: Colors.black,
                            fontWeight: FontWeight.bold,
                            fontSize: 12,
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
                      controller: _tabController,
                      isScrollable: true,
                      indicatorColor: Colors.transparent,
                      dividerColor: Colors.transparent,
                      labelColor: Colors.white,
                      unselectedLabelColor: Colors.white54,
                      labelStyle: GoogleFonts.poppins(fontSize: 18, fontWeight: FontWeight.bold),
                      unselectedLabelStyle: GoogleFonts.poppins(fontSize: 16, fontWeight: FontWeight.w500),
                      padding: const EdgeInsets.symmetric(horizontal: 10),
                      tabs: _tabs.map((tab) => Tab(
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
            ];
          },
          body: Consumer<AppProvider>(
            builder: (context, provider, _) {
              if (provider.isLoading) {
                return const Center(child: CircularProgressIndicator(color: AppColors.accent));
              }

              // Combine categories and wallpapers for a rich feed
              return CustomScrollView(
                physics: const BouncingScrollPhysics(),
                slivers: [
                  // Featured Banner
                  if (provider.banners.isNotEmpty)
                    SliverToBoxAdapter(
                      child: Padding(
                        padding: const EdgeInsets.only(top: 10, bottom: 20),
                        child: CarouselSlider(
                          options: CarouselOptions(
                            height: 220,
                            viewportFraction: 0.92,
                            enlargeCenterPage: true,
                            autoPlay: true,
                            autoPlayCurve: Curves.fastOutSlowIn,
                          ),
                          items: provider.banners.map((banner) {
                            return Builder(
                              builder: (BuildContext context) {
                                return ClipRRect(
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
                                      Positioned(
                                        bottom: 15,
                                        left: 15,
                                        right: 15,
                                        child: Column(
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          children: [
                                            Text(
                                              "Villains Beware My Mommy",
                                              style: GoogleFonts.poppins(
                                                color: Colors.white,
                                                fontSize: 12,
                                                fontWeight: FontWeight.w500,
                                              ),
                                            ),
                                            Text(
                                              "PUNCHES HARD",
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
                                );
                              },
                            );
                          }).toList(),
                        ),
                      ),
                    ),

                  // Grid Content
                  SliverPadding(
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    sliver: SliverGrid(
                      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                        crossAxisCount: 3,
                        childAspectRatio: 0.65,
                        crossAxisSpacing: 12,
                        mainAxisSpacing: 20,
                      ),
                      delegate: SliverChildBuilderDelegate(
                        (context, index) {
                          // Use wallpapers if available, else placeholders
                          // Mapped to Wallpaper model properties
                          final wallpaper = index < provider.wallpapers.length ? provider.wallpapers[index] : null;
                          final String imageUrl = wallpaper?.url ?? "https://via.placeholder.com/150";
                          final String title = wallpaper?.category ?? "Wallpaper";

                          return GestureDetector(
                            onTap: () {
                              if (wallpaper != null) {
                                Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                    builder: (context) => WallpaperDetailScreen(wallpaper: wallpaper),
                                  ),
                                );
                              }
                            },
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Expanded(
                                  child: Stack(
                                    children: [
                                      ClipRRect(
                                        borderRadius: BorderRadius.circular(8),
                                        child: CachedNetworkImage(
                                          imageUrl: imageUrl,
                                          fit: BoxFit.cover,
                                          width: double.infinity,
                                          height: double.infinity,
                                          placeholder: (context, url) => Container(color: Colors.grey[900]),
                                          errorWidget: (context, url, error) => Container(color: Colors.grey[900]),
                                        ),
                                      ),
                                      // Tag (e.g., Exclusive, Sweet)
                                      Positioned(
                                        top: 0,
                                        right: 0,
                                        child: Container(
                                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                          decoration: const BoxDecoration(
                                            color: Color(0xFFFDD835),
                                            borderRadius: BorderRadius.only(
                                              topRight: Radius.circular(8),
                                              bottomLeft: Radius.circular(8),
                                            ),
                                          ),
                                          child: Text(
                                            index % 2 == 0 ? "Exclusive" : "Hot",
                                            style: GoogleFonts.poppins(
                                              color: Colors.black,
                                              fontSize: 10,
                                              fontWeight: FontWeight.bold,
                                            ),
                                          ),
                                        ),
                                      ),
                                      // Bottom Gradient
                                      Positioned(
                                        bottom: 0,
                                        left: 0,
                                        right: 0,
                                        child: Container(
                                          height: 40,
                                          decoration: BoxDecoration(
                                            borderRadius: const BorderRadius.only(
                                              bottomLeft: Radius.circular(8),
                                              bottomRight: Radius.circular(8),
                                            ),
                                            gradient: LinearGradient(
                                              begin: Alignment.bottomCenter,
                                              end: Alignment.topCenter,
                                              colors: [
                                                Colors.black.withOpacity(0.8),
                                                Colors.transparent,
                                              ],
                                            ),
                                          ),
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                                const SizedBox(height: 8),
                                Text(
                                  title,
                                  maxLines: 2,
                                  overflow: TextOverflow.ellipsis,
                                  style: GoogleFonts.poppins(
                                    color: Colors.white,
                                    fontSize: 12,
                                    fontWeight: FontWeight.w500,
                                  ),
                                ),
                                Text(
                                  "Sweet",
                                  style: GoogleFonts.poppins(
                                    color: Colors.grey,
                                    fontSize: 10,
                                  ),
                                ),
                              ],
                            ),
                          ).animate().fadeIn(delay: (50 * index).ms);
                        },
                        childCount: provider.wallpapers.isNotEmpty ? provider.wallpapers.length : 12,
                      ),
                    ),
                  ),
                  
                  SliverToBoxAdapter(child: const SizedBox(height: 120)), // Space for bottom nav
                ],
              );
            },
          ),
        ),
      ),
    );
  }
}
