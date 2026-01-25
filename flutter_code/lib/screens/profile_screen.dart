import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:url_launcher/url_launcher.dart';
import '../services/api_service.dart';
import 'auth/login_screen.dart';
import '../providers/app_provider.dart';
import '../providers/language_provider.dart';
import '../utils/app_theme.dart';
import 'faq_screen.dart';
import 'edit_profile_screen.dart';
import 'support/support_screen.dart';
import 'page_viewer_screen.dart';
import '../widgets/toast/professional_toast.dart';
import 'rewards/reward_history_screen.dart';
import 'rewards/redeem_screen.dart';
import 'referral_screen.dart';

class ProfileScreen extends StatelessWidget {
  const ProfileScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Consumer<LanguageProvider>(
      builder: (context, languageProvider, _) {
        return Scaffold(
          backgroundColor: AppTheme.darkBackgroundColor,
          body: CustomScrollView(
            physics: const BouncingScrollPhysics(),
            slivers: [
              _buildSliverAppBar(context, languageProvider),
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 20.0),
                  child: Column(
                    children: [
                      const SizedBox(height: 24),
                      _buildStatsCard(context, languageProvider),
                      const SizedBox(height: 32),
                      _buildMenuSection(
                        context,
                        title: languageProvider.getText('account_section'),
                        items: [
                          _MenuItem(
                            icon: Icons.person_outline_rounded,
                            title: languageProvider.getText('edit_profile'),
                            onTap: () {
                              Navigator.push(
                                context,
                                MaterialPageRoute(builder: (context) => const EditProfileScreen()),
                              );
                            },
                          ),
                          _MenuItem(
                            icon: Icons.notifications_outlined,
                            title: languageProvider.getText('notifications'),
                            badge: "2",
                            onTap: () {
                              ProfessionalToast.showSuccess(
                                context,
                                message: languageProvider.getText('coming_soon'),
                              );
                            },
                          ),
                          _MenuItem(
                            icon: Icons.wallet_giftcard,
                            title: languageProvider.getText('my_rewards'),
                            onTap: () {
                              Navigator.push(
                                context,
                                MaterialPageRoute(builder: (context) => const RewardHistoryScreen()),
                              );
                            },
                          ),
                          _MenuItem(
                            icon: Icons.monetization_on_outlined,
                            title: languageProvider.getText('redeem') == 'redeem' ? 'Redeem' : languageProvider.getText('redeem'),
                            onTap: () {
                              Navigator.push(
                                context,
                                MaterialPageRoute(builder: (context) => const RedeemScreen()),
                              );
                            },
                          ),
                          _MenuItem(
                            icon: Icons.group_add_rounded,
                            title: "Refer & Earn",
                            onTap: () {
                              Navigator.push(
                                context,
                                MaterialPageRoute(builder: (context) => const ReferralScreen()),
                              );
                            },
                          ),
                        ],
                      ),
                      const SizedBox(height: 24),
                      _buildMenuSection(
                        context,
                        title: languageProvider.getText('general_section'),
                        items: [
                          _MenuItem(
                            icon: Icons.language,
                            title: languageProvider.getText('language'),
                            subtitle: languageProvider.currentLanguageCode == 'en' ? 'English' : 'বাংলা',
                            onTap: () {
                              _showLanguageBottomSheet(context, languageProvider);
                            },
                          ),
                          _MenuItem(
                            icon: Icons.dark_mode_outlined,
                            title: languageProvider.getText('dark_mode'),
                            isSwitch: true,
                            switchValue: true,
                            onChanged: (val) {
                              ProfessionalToast.showSuccess(
                                context,
                                message: languageProvider.getText('coming_soon'),
                              );
                            },
                          ),
                        ],
                      ),
                      const SizedBox(height: 24),
                      _buildMenuSection(
                        context,
                        title: languageProvider.getText('support_section'),
                        items: [
                          _MenuItem(
                            icon: Icons.quiz_rounded,
                            title: languageProvider.getText('faq'),
                            onTap: () {
                              Navigator.push(
                                context,
                                MaterialPageRoute(builder: (context) => const FaqScreen()),
                              );
                            },
                          ),
                          _MenuItem(
                            icon: Icons.help_outline_rounded,
                            title: languageProvider.getText('help_center'),
                            onTap: () {
                              Navigator.push(
                                context,
                                MaterialPageRoute(builder: (context) => const SupportScreen()),
                              );
                            },
                          ),
                          _MenuItem(
                            icon: Icons.privacy_tip_outlined,
                            title: languageProvider.getText('privacy_policy'),
                            onTap: () {
                              Navigator.push(
                                context,
                                MaterialPageRoute(
                                  builder: (context) => PageViewerScreen(
                                    slug: 'privacy-policy',
                                    title: languageProvider.getText('privacy_policy'),
                                  ),
                                ),
                              );
                            },
                          ),
                          _MenuItem(
                            icon: Icons.description_outlined,
                            title: languageProvider.getText('terms_conditions'),
                            onTap: () {
                              Navigator.push(
                                context,
                                MaterialPageRoute(
                                  builder: (context) => PageViewerScreen(
                                    slug: 'terms-and-conditions',
                                    title: languageProvider.getText('terms_conditions'),
                                  ),
                                ),
                              );
                            },
                          ),
                          _MenuItem(
                            icon: Icons.info_outline_rounded,
                            title: languageProvider.getText('about_us'),
                            onTap: () {
                              Navigator.push(
                                context,
                                MaterialPageRoute(
                                  builder: (context) => PageViewerScreen(
                                    slug: 'about-us',
                                    title: languageProvider.getText('about_us'),
                                  ),
                                ),
                              );
                            },
                          ),
                        ],
                      ),
                      const SizedBox(height: 32),
                      _buildLogoutButton(context, languageProvider),
                      const SizedBox(height: 16),
                      _buildDeleteAccountButton(context, languageProvider),
                      const SizedBox(height: 40),
                      Text(
                        "${languageProvider.getText('version')} 1.0.0",
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
    );
  }

  void _showLanguageBottomSheet(BuildContext context, LanguageProvider provider) {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      builder: (context) => Container(
        decoration: BoxDecoration(
          color: AppTheme.darkBackgroundColor,
          borderRadius: const BorderRadius.vertical(top: Radius.circular(24)),
          border: Border.all(color: Colors.white.withOpacity(0.1)),
        ),
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(
              provider.getText('select_language'),
              style: GoogleFonts.poppins(
                fontSize: 20,
                fontWeight: FontWeight.bold,
                color: Colors.white,
              ),
            ),
            const SizedBox(height: 24),
            _buildLanguageOption(
              context, 
              provider, 
              'English', 
              'en', 
              '🇺🇸'
            ),
            const SizedBox(height: 12),
            _buildLanguageOption(
              context, 
              provider, 
              'বাংলা', 
              'bn', 
              '🇧🇩'
            ),
            const SizedBox(height: 32),
          ],
        ),
      ),
    );
  }

  Widget _buildLanguageOption(
    BuildContext context, 
    LanguageProvider provider, 
    String name, 
    String code, 
    String flag
  ) {
    final isSelected = provider.currentLanguageCode == code;
    return GestureDetector(
      onTap: () {
        provider.setLocale(code);
        Navigator.pop(context);
      },
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
        decoration: BoxDecoration(
          color: isSelected ? Colors.amber.withOpacity(0.15) : Colors.white.withOpacity(0.05),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(
            color: isSelected ? Colors.amber : Colors.white.withOpacity(0.05)
          ),
        ),
        child: Row(
          children: [
            Text(flag, style: const TextStyle(fontSize: 24)),
            const SizedBox(width: 16),
            Text(
              name,
              style: GoogleFonts.poppins(
                fontSize: 16,
                fontWeight: FontWeight.w600,
                color: isSelected ? Colors.amber : Colors.white,
              ),
            ),
            const Spacer(),
            if (isSelected)
              const Icon(Icons.check_circle_rounded, color: Colors.amber),
          ],
        ),
      ),
    );
  }

  Widget _buildSliverAppBar(BuildContext context, LanguageProvider languageProvider) {
    return SliverAppBar(
      expandedHeight: 280,
      pinned: true,
      backgroundColor: AppTheme.darkBackgroundColor,
      elevation: 0,
      flexibleSpace: FlexibleSpaceBar(
        background: Stack(
          fit: StackFit.expand,
          children: [
            // Background Gradient
            Container(
              decoration: AppTheme.backgroundDecoration,
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
                  Consumer<AppProvider>(
                    builder: (context, provider, _) {
                      return Stack(
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
                              image: DecorationImage(
                                image: provider.userAvatar.isNotEmpty
                                    ? NetworkImage(provider.userAvatar)
                                    : const NetworkImage("https://i.pravatar.cc/300"),
                                fit: BoxFit.cover,
                              ),
                            ),
                          ),
                          Positioned(
                            bottom: 0,
                            right: 0,
                            child: GestureDetector(
                              onTap: () {
                                Navigator.push(
                                  context,
                                  MaterialPageRoute(builder: (context) => const EditProfileScreen()),
                                );
                              },
                              child: Container(
                                padding: const EdgeInsets.all(8),
                                decoration: const BoxDecoration(
                                  color: Colors.amber,
                                  shape: BoxShape.circle,
                                ),
                                child: const Icon(Icons.camera_alt, color: Colors.black, size: 16),
                              ),
                            ),
                          ),
                        ],
                      ).animate().scale(duration: 600.ms, curve: Curves.elasticOut);
                    }
                  ),
                  const SizedBox(height: 16),
                  
                  Consumer<AppProvider>(
                    builder: (context, provider, child) {
                      return Column(
                        children: [
                          Text(
                            provider.userName.isNotEmpty ? provider.userName : languageProvider.getText('guest_user'),
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
                              languageProvider.getText('user_id_fmt').replaceAll('@id', provider.userId),
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

  Widget _buildStatsCard(BuildContext context, LanguageProvider languageProvider) {
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
                languageProvider.getText('balance'),
                "${provider.coins}",
                FontAwesomeIcons.coins,
                Colors.amber,
              ),
              Container(width: 1, height: 40, color: Colors.white10),
              _buildStatItem(
                languageProvider.getText('level'),
                "${provider.userLevel}",
                Icons.shield_rounded,
                Colors.blueAccent,
              ),
              Container(width: 1, height: 40, color: Colors.white10),
              _buildStatItem(
                languageProvider.getText('tasks_stat'),
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
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
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

  Widget _buildLogoutButton(BuildContext context, LanguageProvider provider) {
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
          onTap: () {
            _showLogoutDialog(context, provider);
          },
          borderRadius: BorderRadius.circular(16),
          child: Center(
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Icon(Icons.logout_rounded, color: Colors.redAccent),
                const SizedBox(width: 8),
                Text(
                  provider.getText('logout'),
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

  Widget _buildDeleteAccountButton(BuildContext context, LanguageProvider provider) {
    return Container(
      width: double.infinity,
      height: 56,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.red.withOpacity(0.3)),
        color: Colors.red.withOpacity(0.05),
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: () {
            _showDeleteAccountDialog(context, provider);
          },
          borderRadius: BorderRadius.circular(16),
          child: Center(
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Icon(Icons.delete_forever_rounded, color: Colors.red),
                const SizedBox(width: 8),
                Text(
                  provider.getText('delete_account') == 'delete_account' ? 'Delete Account' : provider.getText('delete_account'),
                  style: GoogleFonts.poppins(
                    fontSize: 16,
                    fontWeight: FontWeight.w600,
                    color: Colors.red,
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    ).animate().fadeIn().slideY(begin: 1, end: 0, delay: 100.ms);
  }

  void _showLogoutDialog(BuildContext context, LanguageProvider provider) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        backgroundColor: const Color(0xFF1F1B2E),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(20),
          side: BorderSide(color: Colors.white.withOpacity(0.1)),
        ),
        title: Text(
          provider.getText('logout'),
          style: GoogleFonts.poppins(
            color: Colors.white,
            fontWeight: FontWeight.bold,
          ),
        ),
        content: Text(
          "Are you sure you want to logout?",
          style: GoogleFonts.poppins(
            color: Colors.white70,
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: Text(
              provider.getText('cancel') == 'cancel' ? 'Cancel' : provider.getText('cancel'),
              style: GoogleFonts.poppins(color: Colors.white54),
            ),
          ),
          TextButton(
            onPressed: () async {
              Navigator.pop(context);
              final appProvider = Provider.of<AppProvider>(context, listen: false);
              await appProvider.logout();
              if (context.mounted) {
                Navigator.of(context).pushAndRemoveUntil(
                  MaterialPageRoute(builder: (context) => const LoginScreen()),
                  (route) => false,
                );
              }
            },
            child: Text(
              provider.getText('logout'),
              style: GoogleFonts.poppins(color: Colors.redAccent, fontWeight: FontWeight.bold),
            ),
          ),
        ],
      ),
    );
  }

  void _showDeleteAccountDialog(BuildContext context, LanguageProvider provider) {
    showDialog(
      context: context,
      builder: (dialogContext) => AlertDialog(
        backgroundColor: const Color(0xFF1F1B2E),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(20),
          side: BorderSide(color: Colors.white.withOpacity(0.1)),
        ),
        title: Text(
          provider.getText('delete_account') == 'delete_account' ? 'Delete Account' : provider.getText('delete_account'),
          style: GoogleFonts.poppins(
            color: Colors.white,
            fontWeight: FontWeight.bold,
          ),
        ),
        content: Text(
          "Are you sure you want to delete your account? This action cannot be undone and you will lose all your data.",
          style: GoogleFonts.poppins(
            color: Colors.white70,
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(dialogContext),
            child: Text(
              provider.getText('cancel') == 'cancel' ? 'Cancel' : provider.getText('cancel'),
              style: GoogleFonts.poppins(color: Colors.white54),
            ),
          ),
          TextButton(
            onPressed: () async {
              Navigator.pop(dialogContext);
              _handleDeleteAccount(context);
            },
            child: Text(
              provider.getText('delete') == 'delete' ? 'Delete' : provider.getText('delete'),
              style: GoogleFonts.poppins(color: Colors.redAccent, fontWeight: FontWeight.bold),
            ),
          ),
        ],
      ),
    );
  }

  Future<void> _handleDeleteAccount(BuildContext context) async {
    showDialog(
      context: context,
      barrierDismissible: false,
      useRootNavigator: true,
      builder: (context) => const Center(child: CircularProgressIndicator(color: Colors.amber)),
    );

    final apiService = ApiService();
    final success = await apiService.deleteAccount();

    if (context.mounted) {
      Navigator.of(context, rootNavigator: true).pop(); // Hide loading

      if (success) {
        final appProvider = Provider.of<AppProvider>(context, listen: false);
        await appProvider.logout();
        
        if (context.mounted) {
          ProfessionalToast.showSuccess(
            context, 
            message: "Account deleted successfully",
          );

          Navigator.of(context).pushAndRemoveUntil(
            MaterialPageRoute(builder: (context) => const LoginScreen()),
            (route) => false,
          );
        }
      } else {
        ProfessionalToast.showError(
          context,
          message: "Failed to delete account. Please try again.",
        );
      }
    }
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
