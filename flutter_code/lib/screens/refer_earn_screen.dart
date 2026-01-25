import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:share_plus/share_plus.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import '../providers/language_provider.dart';
import '../services/api_service.dart';
import '../utils/app_theme.dart';
import '../widgets/toast/professional_toast.dart';

class ReferEarnScreen extends StatefulWidget {
  const ReferEarnScreen({super.key});

  @override
  State<ReferEarnScreen> createState() => _ReferEarnScreenState();
}

class _ReferEarnScreenState extends State<ReferEarnScreen> {
  final ApiService _apiService = ApiService();
  bool _isLoading = true;
  String _referralCode = "";
  int _referralCount = 0;
  int _totalEarnings = 0;
  List<dynamic> _referralHistory = [];

  @override
  void initState() {
    super.initState();
    _fetchReferralData();
  }

  Future<void> _fetchReferralData() async {
    try {
      final response = await _apiService.getUserDetails();
      if (response['success'] == true && response['data'] != null) {
        final data = response['data'];
        final user = data['user'];
        setState(() {
          _referralCode = user['referral_code'] ?? 'N/A';
          _referralCount = data['referral_count'] ?? 0;
          _totalEarnings = int.tryParse(data['total_referral_earnings'].toString()) ?? 0;
          _referralHistory = data['referrals'] ?? [];
          _isLoading = false;
        });
      }
    } catch (e) {
      print('Error fetching referral data: $e');
      setState(() => _isLoading = false);
    }
  }

  void _copyToClipboard() {
    Clipboard.setData(ClipboardData(text: _referralCode));
    ProfessionalToast.showSuccess(context, message: "Referral code copied!");
  }

  void _shareReferral() {
    Share.share(
      'Join me on this amazing app! Use my referral code: $_referralCode to get a signup bonus! Download now.',
    );
  }

  @override
  Widget build(BuildContext context) {
    return Consumer<LanguageProvider>(
      builder: (context, languageProvider, _) {
        return Scaffold(
          backgroundColor: AppTheme.darkBackgroundColor,
          extendBodyBehindAppBar: true,
          appBar: AppBar(
            backgroundColor: Colors.transparent,
            elevation: 0,
            leading: IconButton(
              icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white),
              onPressed: () => Navigator.pop(context),
            ),
            title: Text(
              "Refer & Earn",
              style: GoogleFonts.poppins(fontWeight: FontWeight.bold, color: Colors.white),
            ),
            centerTitle: true,
          ),
          body: Stack(
            children: [
              // Background Gradient
              Container(
                decoration: const BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topCenter,
                    end: Alignment.bottomCenter,
                    colors: [
                      Color(0xFF2A2D3E),
                      AppTheme.darkBackgroundColor,
                    ],
                  ),
                ),
              ),
              
              if (_isLoading)
                const Center(child: CircularProgressIndicator(color: Colors.amber))
              else
                SingleChildScrollView(
                  physics: const BouncingScrollPhysics(),
                  padding: const EdgeInsets.fromLTRB(20, 100, 20, 20),
                  child: Column(
                    children: [
                      _buildHeaderCard(languageProvider),
                      const SizedBox(height: 24),
                      _buildStatsRow(languageProvider),
                      const SizedBox(height: 24),
                      _buildReferralHistoryList(languageProvider),
                    ],
                  ),
                ),
            ],
          ),
        );
      }
    );
  }

  Widget _buildHeaderCard(LanguageProvider languageProvider) {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [Color(0xFF6366F1), Color(0xFF4F46E5)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(24),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF4F46E5).withOpacity(0.4),
            blurRadius: 20,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.2),
              shape: BoxShape.circle,
            ),
            child: const Icon(FontAwesomeIcons.gift, color: Colors.white, size: 40),
          ),
          const SizedBox(height: 16),
          Text(
            "Invite Friends & Earn",
            style: GoogleFonts.poppins(
              fontSize: 22,
              fontWeight: FontWeight.bold,
              color: Colors.white,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            "Share your code with friends and earn coins when they sign up!",
            textAlign: TextAlign.center,
            style: GoogleFonts.poppins(
              fontSize: 14,
              color: Colors.white.withOpacity(0.9),
            ),
          ),
          const SizedBox(height: 24),
          
          // Referral Code Box
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
            decoration: BoxDecoration(
              color: Colors.black.withOpacity(0.2),
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: Colors.white.withOpacity(0.3), style: BorderStyle.solid),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      "Your Referral Code",
                      style: GoogleFonts.poppins(fontSize: 10, color: Colors.white70),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      _referralCode,
                      style: GoogleFonts.poppins(
                        fontSize: 20,
                        fontWeight: FontWeight.bold,
                        color: Colors.amber,
                        letterSpacing: 1.5,
                      ),
                    ),
                  ],
                ),
                IconButton(
                  onPressed: _copyToClipboard,
                  icon: const Icon(Icons.copy_rounded, color: Colors.white),
                  tooltip: "Copy Code",
                ),
              ],
            ),
          ),
          const SizedBox(height: 20),
          
          // Share Button
          SizedBox(
            width: double.infinity,
            child: ElevatedButton.icon(
              onPressed: _shareReferral,
              icon: const Icon(Icons.share_rounded, color: Colors.black),
              label: Text(
                "Invite Now",
                style: GoogleFonts.poppins(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                  color: Colors.black,
                ),
              ),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.amber,
                padding: const EdgeInsets.symmetric(vertical: 16),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                elevation: 0,
              ),
            ),
          ),
        ],
      ),
    ).animate().fadeIn().slideY(begin: 0.2, end: 0);
  }

  Widget _buildStatsRow(LanguageProvider languageProvider) {
    return Row(
      children: [
        Expanded(child: _buildStatCard("Total Referrals", "$_referralCount", Icons.people_outline_rounded, Colors.blueAccent)),
        const SizedBox(width: 16),
        Expanded(child: _buildStatCard("Total Earnings", "$_totalEarnings", FontAwesomeIcons.coins, Colors.amber)),
      ],
    ).animate().fadeIn(delay: 200.ms).slideY(begin: 0.2, end: 0);
  }

  Widget _buildStatCard(String title, String value, IconData icon, Color color) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: const Color(0xFF1F1B2E),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.white.withOpacity(0.05)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: color, size: 24),
          const SizedBox(height: 12),
          Text(
            value,
            style: GoogleFonts.poppins(
              fontSize: 24,
              fontWeight: FontWeight.bold,
              color: Colors.white,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            title,
            style: GoogleFonts.poppins(
              fontSize: 12,
              color: Colors.white54,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildReferralHistoryList(LanguageProvider languageProvider) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          "Referral History",
          style: GoogleFonts.poppins(
            fontSize: 18,
            fontWeight: FontWeight.bold,
            color: Colors.white,
          ),
        ),
        const SizedBox(height: 16),
        if (_referralHistory.isEmpty)
          Center(
            child: Padding(
              padding: const EdgeInsets.symmetric(vertical: 40),
              child: Column(
                children: [
                  Icon(Icons.person_off_outlined, size: 48, color: Colors.white.withOpacity(0.2)),
                  const SizedBox(height: 16),
                  Text(
                    "No referrals yet",
                    style: GoogleFonts.poppins(color: Colors.white38),
                  ),
                ],
              ),
            ),
          )
        else
          ListView.separated(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            itemCount: _referralHistory.length,
            separatorBuilder: (context, index) => const SizedBox(height: 12),
            itemBuilder: (context, index) {
              final item = _referralHistory[index];
              return Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: const Color(0xFF1F1B2E),
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: Colors.white.withOpacity(0.05)),
                ),
                child: Row(
                  children: [
                    CircleAvatar(
                      backgroundColor: Colors.white10,
                      backgroundImage: item['avatar'] != null && item['avatar'].toString().startsWith('http') 
                          ? NetworkImage(item['avatar']) 
                          : null,
                      child: item['avatar'] == null || !item['avatar'].toString().startsWith('http')
                          ? const Icon(Icons.person, color: Colors.white70)
                          : null,
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            item['name'] ?? 'Unknown',
                            style: GoogleFonts.poppins(
                              fontWeight: FontWeight.w600,
                              color: Colors.white,
                            ),
                          ),
                          Text(
                            "Level ${item['level']}",
                            style: GoogleFonts.poppins(
                              fontSize: 12,
                              color: item['level'] == 1 ? Colors.greenAccent : Colors.orangeAccent,
                            ),
                          ),
                        ],
                      ),
                    ),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.end,
                      children: [
                        Text(
                          "+${item['bonus']}",
                          style: GoogleFonts.poppins(
                            fontWeight: FontWeight.bold,
                            color: Colors.amber,
                          ),
                        ),
                        Text(
                          "Coins",
                          style: GoogleFonts.poppins(
                            fontSize: 10,
                            color: Colors.amber.withOpacity(0.7),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ).animate().fadeIn(delay: Duration(milliseconds: 50 * index)).slideX();
            },
          ),
      ],
    );
  }
}
