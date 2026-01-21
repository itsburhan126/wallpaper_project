import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:glassmorphism/glassmorphism.dart';
import 'package:provider/provider.dart';
import 'package:share_plus/share_plus.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import '../providers/app_provider.dart';
import '../utils/constants.dart';
import '../utils/app_theme.dart';
import '../widgets/toast/professional_toast.dart';

class ReferralScreen extends StatelessWidget {
  const ReferralScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.darkBackgroundColor, // Deep Purple/Black background
      body: Stack(
        children: [
          // Gradient Header Background (Matches Task Screen AppBar)
          Positioned(
            top: 0,
            left: 0,
            right: 0,
            height: 300,
            child: Container(
              decoration: AppTheme.backgroundDecoration,
            ),
          ),
          SafeArea(
            child: Column(
            children: [
              // Custom AppBar
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                child: Row(
                  children: [
                    IconButton(
                      onPressed: () => Navigator.pop(context),
                      icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white, size: 20),
                      style: IconButton.styleFrom(
                        backgroundColor: Colors.white.withOpacity(0.1),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                    ),
                    const SizedBox(width: 16),
                    Text(
                      "Refer & Earn",
                      style: GoogleFonts.poppins(
                        fontSize: 20,
                        fontWeight: FontWeight.bold,
                        color: Colors.white,
                      ),
                    ),
                  ],
                ),
              ),

              Expanded(
                child: SingleChildScrollView(
                  physics: const BouncingScrollPhysics(),
                  padding: const EdgeInsets.symmetric(horizontal: 24),
                  child: Consumer<AppProvider>(
                    builder: (context, provider, child) {
                      final referralCode = provider.referralCode.isNotEmpty 
                          ? provider.referralCode 
                          : "Loading...";
                      
                      return Column(
                        crossAxisAlignment: CrossAxisAlignment.center,
                        children: [
                          const SizedBox(height: 20),
                          
                          // Hero Image / Icon
                          Container(
                            height: 120,
                            width: 120,
                            decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              gradient: const LinearGradient(
                                colors: [Color(0xFFD946EF), Color(0xFF8B5CF6)],
                                begin: Alignment.topLeft,
                                end: Alignment.bottomRight,
                              ),
                              boxShadow: [
                                BoxShadow(
                                  color: const Color(0xFFD946EF).withOpacity(0.4),
                                  blurRadius: 30,
                                  spreadRadius: 5,
                                ),
                              ],
                            ),
                            child: const Icon(
                              FontAwesomeIcons.gift,
                              size: 50,
                              color: Colors.white,
                            ),
                          ).animate().scale(duration: 600.ms, curve: Curves.elasticOut),

                          const SizedBox(height: 32),

                          Text(
                            "Invite Friends,\nGet Amazing Rewards!",
                            textAlign: TextAlign.center,
                            style: GoogleFonts.poppins(
                              fontSize: 24,
                              fontWeight: FontWeight.bold,
                              color: Colors.white,
                              height: 1.2,
                            ),
                          ).animate().fadeIn(delay: 200.ms).moveY(begin: 10, end: 0),

                          const SizedBox(height: 12),

                          Text(
                            "Share your code with friends and earn 500 coins for every friend who joins!",
                            textAlign: TextAlign.center,
                            style: GoogleFonts.poppins(
                              fontSize: 14,
                              color: Colors.white70,
                              height: 1.5,
                            ),
                          ).animate().fadeIn(delay: 300.ms),

                          const SizedBox(height: 40),

                          // Referral Code Box
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
                            decoration: BoxDecoration(
                              color: Colors.white.withOpacity(0.05),
                              borderRadius: BorderRadius.circular(20),
                              border: Border.all(color: Colors.white.withOpacity(0.1)),
                            ),
                            child: Column(
                              children: [
                                Text(
                                  "YOUR REFERRAL CODE",
                                  style: GoogleFonts.poppins(
                                    color: Colors.white38,
                                    fontSize: 12,
                                    fontWeight: FontWeight.w600,
                                    letterSpacing: 1.5,
                                  ),
                                ),
                                const SizedBox(height: 12),
                                Row(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    Text(
                                      referralCode,
                                      style: GoogleFonts.spaceMono(
                                        color: Colors.white,
                                        fontSize: 28,
                                        fontWeight: FontWeight.bold,
                                        letterSpacing: 2.0,
                                      ),
                                    ),
                                    const SizedBox(width: 16),
                                    IconButton(
                                      onPressed: () {
                                        if (referralCode != "Loading...") {
                                          Clipboard.setData(ClipboardData(text: referralCode));
                                          ProfessionalToast.showSuccess(context, message: "Code copied!");
                                        }
                                      },
                                      icon: const Icon(Icons.copy_rounded, color: Colors.white70),
                                      style: IconButton.styleFrom(
                                        backgroundColor: Colors.white.withOpacity(0.1),
                                      ),
                                    ),
                                  ],
                                ),
                              ],
                            ),
                          ).animate().fadeIn(delay: 400.ms).moveY(begin: 20, end: 0),

                          const SizedBox(height: 40),

                          // Stats Section
                          Row(
                            children: [
                              _buildStatCard(
                                "Total Referrals",
                                "${provider.totalReferrals}",
                                Icons.people_outline_rounded,
                                Colors.blueAccent,
                              ),
                              const SizedBox(width: 16),
                              _buildStatCard(
                                "Total Earnings",
                                "${provider.totalReferralEarnings}",
                                Icons.monetization_on_outlined,
                                Colors.amber,
                                isCoin: true,
                              ),
                            ],
                          ).animate().fadeIn(delay: 500.ms),

                          const SizedBox(height: 40),

                          // How it works
                          Align(
                            alignment: Alignment.centerLeft,
                            child: Text(
                              "How it works",
                              style: GoogleFonts.poppins(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                                color: Colors.white,
                              ),
                            ),
                          ),
                          const SizedBox(height: 16),
                          _buildStep(1, "Share your code with friends"),
                          _buildStep(2, "They sign up using your code"),
                          _buildStep(3, "You both earn rewards!"),
                          
                          const SizedBox(height: 40), // Bottom padding
                        ],
                      );
                    },
                  ),
                ),
              ),

              // Bottom Share Button
              Container(
                padding: const EdgeInsets.all(24),
                decoration: BoxDecoration(
                  color: AppTheme.darkBackgroundColor,
                  border: Border(top: BorderSide(color: Colors.white.withOpacity(0.05))),
                ),
                child: Consumer<AppProvider>(
                  builder: (context, provider, _) {
                    return SizedBox(
                      width: double.infinity,
                      height: 56,
                      child: ElevatedButton(
                        onPressed: () {
                          if (provider.referralCode.isNotEmpty) {
                            Share.share(
                              "Hey! Use my referral code *${provider.referralCode}* to sign up and get a bonus! Download the app now!",
                              subject: "Join me and earn rewards!"
                            );
                          }
                        },
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFFD946EF),
                          foregroundColor: Colors.white,
                          elevation: 8,
                          shadowColor: const Color(0xFFD946EF).withOpacity(0.5),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                        ),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            const Icon(Icons.share_rounded, size: 22),
                            const SizedBox(width: 12),
                            Text(
                              "Invite Friends Now",
                              style: GoogleFonts.poppins(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ],
                        ),
                      ),
                    );
                  }
                ),
              ).animate().slideY(begin: 1, end: 0, duration: 600.ms, curve: Curves.easeOutQuint),
            ],
          ),
        ),
        ],
      ),
    );
  }

  Widget _buildStatCard(String title, String value, IconData icon, Color color, {bool isCoin = false}) {
    return Expanded(
      child: GlassmorphicContainer(
        width: double.infinity,
        height: 100,
        borderRadius: 20,
        blur: 20,
        alignment: Alignment.center,
        border: 1,
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
            Colors.white.withOpacity(0.2),
            Colors.white.withOpacity(0.05),
          ],
        ),
        child: Padding(
          padding: const EdgeInsets.all(16.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Row(
                children: [
                  Icon(icon, color: color, size: 18),
                  const SizedBox(width: 8),
                  Text(
                    title,
                    style: GoogleFonts.poppins(
                      color: Colors.white70,
                      fontSize: 11,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 8),
              Text(
                value,
                style: GoogleFonts.poppins(
                  color: isCoin ? Colors.amber : Colors.white,
                  fontSize: 22,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildStep(int number, String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 16),
      child: Row(
        children: [
          Container(
            width: 28,
            height: 28,
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.1),
              shape: BoxShape.circle,
            ),
            child: Center(
              child: Text(
                "$number",
                style: GoogleFonts.poppins(
                  color: Colors.white,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Text(
              text,
              style: GoogleFonts.poppins(
                color: Colors.white70,
                fontSize: 14,
              ),
            ),
          ),
        ],
      ),
    );
  }
}
