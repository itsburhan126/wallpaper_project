import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:url_launcher/url_launcher.dart';
import '../providers/app_provider.dart';

class ProfileScreen extends StatelessWidget {
  const ProfileScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF120C24),
      body: CustomScrollView(
        physics: const BouncingScrollPhysics(),
        slivers: [
          _buildSliverAppBar(context),
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20.0),
              child: Column(
                children: [
                  const SizedBox(height: 24),
                  _buildStatsCard(context),
                  const SizedBox(height: 32),
                  _buildMenuSection(
                    context,
                    title: "Account",
                    items: [
                      _MenuItem(
                        icon: Icons.person_outline_rounded,
                        title: "Edit Profile",
                        onTap: () {
                          // Navigate to edit profile
                        },
                      ),
                      _MenuItem(
                        icon: Icons.notifications_outlined,
                        title: "Notifications",
                        badge: "2",
                        onTap: () {},
                      ),
                      _MenuItem(
                        icon: Icons.wallet_giftcard,
                        title: "My Rewards",
                        onTap: () {},
                      ),
                    ],
                  ),
                  const SizedBox(height: 24),
                  _buildMenuSection(
                    context,
                    title: "General",
                    items: [
                      _MenuItem(
                        icon: Icons.language,
                        title: "Language",
                        subtitle: "English (US)",
                        onTap: () {},
                      ),
                      _MenuItem(
                        icon: Icons.dark_mode_outlined,
                        title: "Dark Mode",
                        isSwitch: true,
                        switchValue: true,
                        onChanged: (val) {},
                      ),
                    ],
                  ),
                  const SizedBox(height: 24),
                  _buildMenuSection(
                    context,
                    title: "Support",
                    items: [
                      _MenuItem(
                        icon: Icons.help_outline_rounded,
                        title: "Help Center",
                        onTap: () {},
                      ),
                      _MenuItem(
                        icon: Icons.privacy_tip_outlined,
                        title: "Privacy Policy",
                        onTap: () async {
                          const url = 'https://google.com'; // Replace with actual policy URL
                          if (await canLaunchUrl(Uri.parse(url))) {
                            await launchUrl(Uri.parse(url));
                          }
                        },
                      ),
                      _MenuItem(
                        icon: Icons.info_outline_rounded,
                        title: "About Us",
                        onTap: () {},
                      ),
                    ],
                  ),
                  const SizedBox(height: 32),
                  _buildLogoutButton(),
                  const SizedBox(height: 40),
                  Text(
                    "Version 1.0.0",
                    style: GoogleFonts.poppins(
                      color: Colors.white24,
                      fontSize: 12,
                    ),
                  ),
                  const SizedBox(height: 100),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSliverAppBar(BuildContext context) {
    return SliverAppBar(
      expandedHeight: 280,
      pinned: true,
      backgroundColor: const Color(0xFF120C24),
      elevation: 0,
      flexibleSpace: FlexibleSpaceBar(
        background: Stack(
          fit: StackFit.expand,
          children: [
            // Background Gradient
            Container(
              decoration: const BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                  colors: [
                    Color(0xFF4A00E0),
                    Color(0xFF8E2DE2),
                    Color(0xFF120C24),
                  ],
                  stops: [0.0, 0.6, 1.0],
                ),
              ),
            ),
            // Pattern Overlay
            Positioned.fill(
              child: Opacity(
                opacity: 0.1,
                child: Image.network(
                  "https://www.transparenttextures.com/patterns/cubes.png", // Subtle pattern
                  repeat: ImageRepeat.repeat,
                  errorBuilder: (context, error, stackTrace) => const SizedBox(),
                ),
              ),
            ),
            // Content
            SafeArea(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const SizedBox(height: 20),
                  // Avatar with Glow
                  Stack(
                    alignment: Alignment.center,
                    children: [
                      Container(
                        width: 110,
                        height: 110,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          color: Colors.white.withOpacity(0.1),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withOpacity(0.2),
                              blurRadius: 20,
                              spreadRadius: 5,
                            ),
                          ],
                        ),
                      ),
                      Container(
                        width: 100,
                        height: 100,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          border: Border.all(color: Colors.white, width: 3),
                          image: const DecorationImage(
                            image: NetworkImage("https://i.pravatar.cc/300"), // Placeholder Avatar
                            fit: BoxFit.cover,
                          ),
                        ),
                      ),
                      Positioned(
                        bottom: 0,
                        right: 0,
                        child: Container(
                          padding: const EdgeInsets.all(8),
                          decoration: const BoxDecoration(
                            color: Colors.amber,
                            shape: BoxShape.circle,
                          ),
                          child: const Icon(Icons.camera_alt, color: Colors.black, size: 16),
                        ),
                      ),
                    ],
                  ).animate().scale(duration: 600.ms, curve: Curves.elasticOut),
                  const SizedBox(height: 16),
                  
                  Consumer<AppProvider>(
                    builder: (context, provider, child) {
                      return Column(
                        children: [
                          Text(
                            provider.userName.isNotEmpty ? provider.userName : "Guest User",
                            style: GoogleFonts.poppins(
                              fontSize: 24,
                              fontWeight: FontWeight.bold,
                              color: Colors.white,
                            ),
                          ).animate().fadeIn().slideY(begin: 0.5, end: 0),
                          const SizedBox(height: 4),
                          if (provider.userId.isNotEmpty)
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                            decoration: BoxDecoration(
                              color: Colors.white.withOpacity(0.15),
                              borderRadius: BorderRadius.circular(20),
                              border: Border.all(color: Colors.white.withOpacity(0.2)),
                            ),
                            child: Text(
                              "ID: ${provider.userId}",
                              style: GoogleFonts.poppins(
                                fontSize: 12,
                                color: Colors.white70,
                                letterSpacing: 0.5,
                              ),
                            ),
                          ).animate().fadeIn(delay: 200.ms),
                        ],
                      );
                    }
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStatsCard(BuildContext context) {
    return Consumer<AppProvider>(
      builder: (context, provider, _) {
        return Container(
          padding: const EdgeInsets.symmetric(vertical: 20, horizontal: 16),
          decoration: BoxDecoration(
            color: const Color(0xFF1F1B2E),
            borderRadius: BorderRadius.circular(24),
            border: Border.all(color: Colors.white.withOpacity(0.05)),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.2),
                blurRadius: 16,
                offset: const Offset(0, 8),
              ),
            ],
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
              _buildStatItem(
                "Balance",
                "${provider.coins}",
                Icons.monetization_on_rounded,
                Colors.amber,
              ),
              Container(width: 1, height: 40, color: Colors.white10),
              _buildStatItem(
                "Level",
                "${provider.userLevel}",
                Icons.shield_rounded,
                Colors.blueAccent,
              ),
              Container(width: 1, height: 40, color: Colors.white10),
              _buildStatItem(
                "Tasks",
                "158",
                Icons.check_circle_rounded,
                Colors.greenAccent,
              ),
            ],
          ),
        ).animate().slideY(begin: 0.2, end: 0, duration: 500.ms).fadeIn();
      },
    );
  }

  Widget _buildStatItem(String label, String value, IconData icon, Color color) {
    return Column(
      children: [
        Icon(icon, color: color, size: 28),
        const SizedBox(height: 8),
        Text(
          value,
          style: GoogleFonts.poppins(
            fontSize: 18,
            fontWeight: FontWeight.bold,
            color: Colors.white,
          ),
        ),
        Text(
          label,
          style: GoogleFonts.poppins(
            fontSize: 12,
            color: Colors.white54,
          ),
        ),
      ],
    );
  }

  Widget _buildMenuSection(
    BuildContext context, {
    required String title,
    required List<_MenuItem> items,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: const EdgeInsets.only(left: 8, bottom: 12),
          child: Text(
            title,
            style: GoogleFonts.poppins(
              fontSize: 14,
              fontWeight: FontWeight.w600,
              color: Colors.white54,
              letterSpacing: 1,
            ),
          ),
        ),
        Container(
          decoration: BoxDecoration(
            color: const Color(0xFF1F1B2E),
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: Colors.white.withOpacity(0.05)),
          ),
          child: Column(
            children: items.asMap().entries.map((entry) {
              final index = entry.key;
              final item = entry.value;
              return Column(
                children: [
                  _buildListTile(item),
                  if (index != items.length - 1)
                    Divider(
                      height: 1,
                      thickness: 1,
                      color: Colors.white.withOpacity(0.05),
                      indent: 60,
                    ),
                ],
              );
            }).toList(),
          ),
        ),
      ],
    ).animate().fadeIn().slideX(begin: 0.1, end: 0);
  }

  Widget _buildListTile(_MenuItem item) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: item.onTap,
        borderRadius: BorderRadius.circular(20),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
          child: Row(
            children: [
              Container(
                width: 40,
                height: 40,
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.05),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Icon(item.icon, color: Colors.white70, size: 20),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      item.title,
                      style: GoogleFonts.poppins(
                        fontSize: 15,
                        fontWeight: FontWeight.w500,
                        color: Colors.white,
                      ),
                    ),
                    if (item.subtitle != null)
                      Text(
                        item.subtitle!,
                        style: GoogleFonts.poppins(
                          fontSize: 12,
                          color: Colors.white38,
                        ),
                      ),
                  ],
                ),
              ),
              if (item.badge != null)
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                  decoration: BoxDecoration(
                    color: Colors.redAccent,
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Text(
                    item.badge!,
                    style: GoogleFonts.poppins(
                      fontSize: 10,
                      fontWeight: FontWeight.bold,
                      color: Colors.white,
                    ),
                  ),
                ),
              if (item.isSwitch)
                Switch(
                  value: item.switchValue ?? false,
                  onChanged: item.onChanged,
                  activeColor: const Color(0xFF8E2DE2),
                )
              else
                const Icon(Icons.arrow_forward_ios_rounded, color: Colors.white24, size: 16),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildLogoutButton() {
    return Container(
      width: double.infinity,
      height: 56,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.redAccent.withOpacity(0.5)),
        color: Colors.redAccent.withOpacity(0.1),
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: () {},
          borderRadius: BorderRadius.circular(16),
          child: Center(
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Icon(Icons.logout_rounded, color: Colors.redAccent),
                const SizedBox(width: 8),
                Text(
                  "Log Out",
                  style: GoogleFonts.poppins(
                    fontSize: 16,
                    fontWeight: FontWeight.w600,
                    color: Colors.redAccent,
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    ).animate().fadeIn().slideY(begin: 1, end: 0);
  }
}

class _MenuItem {
  final IconData icon;
  final String title;
  final String? subtitle;
  final String? badge;
  final bool isSwitch;
  final bool? switchValue;
  final Function(bool)? onChanged;
  final VoidCallback? onTap;

  _MenuItem({
    required this.icon,
    required this.title,
    this.subtitle,
    this.badge,
    this.isSwitch = false,
    this.switchValue,
    this.onChanged,
    this.onTap,
  });
}
