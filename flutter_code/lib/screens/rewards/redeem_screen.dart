import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:provider/provider.dart';
import 'package:shimmer/shimmer.dart';
import '../../utils/app_theme.dart';
import '../../providers/language_provider.dart';
import '../../services/api_service.dart';
import '../../widgets/toast/professional_toast.dart';
import '../../widgets/ads/universal_banner_ad.dart';

class RedeemScreen extends StatefulWidget {
  const RedeemScreen({super.key});

  @override
  State<RedeemScreen> createState() => _RedeemScreenState();
}

class _RedeemScreenState extends State<RedeemScreen> {
  final ApiService _apiService = ApiService();
  bool _isLoadingGateways = true;
  List<dynamic> _gateways = [];
  int? _selectedGatewayId;
  
  bool _isLoadingMethods = false;
  List<dynamic> _methods = [];
  int? _selectedMethodId;
  
  final _accountController = TextEditingController();
  final _amountController = TextEditingController();
  bool _isSubmitting = false;

  @override
  void initState() {
    super.initState();
    _loadGateways();
  }

  Future<void> _loadGateways() async {
    final gateways = await _apiService.getRedeemGateways();
    if (mounted) {
      setState(() {
        _gateways = gateways;
        _isLoadingGateways = false;
        // Auto select first if available
        if (_gateways.isNotEmpty) {
          _onGatewaySelected(_gateways[0]['id']);
        }
      });
    }
  }

  Future<void> _onGatewaySelected(int gatewayId) async {
    setState(() {
      _selectedGatewayId = gatewayId;
      _isLoadingMethods = true;
      _selectedMethodId = null;
      _methods = [];
    });

    final methods = await _apiService.getRedeemMethods(gatewayId);
    if (mounted) {
      setState(() {
        _methods = methods;
        _isLoadingMethods = false;
      });
    }
  }

  Future<void> _submitRedeem() async {
    if (_selectedMethodId == null) {
      ProfessionalToast.showError(context, message: 'Please select a method');
      return;
    }
    if (_accountController.text.isEmpty) {
      ProfessionalToast.showError(context, message: 'Please enter account details');
      return;
    }

    // Find the selected method to get the amount
    final selectedMethod = _methods.firstWhere((m) => m['id'] == _selectedMethodId, orElse: () => null);
    if (selectedMethod == null) {
      ProfessionalToast.showError(context, message: 'Invalid method selected');
      return;
    }

    final amount = num.tryParse(selectedMethod['amount'].toString()) ?? 0;
    
    // We can also validate coin cost if needed, but the backend should handle the deduction logic
    // final coinCost = int.tryParse(selectedMethod['coin_cost'].toString()) ?? 0;

    if (amount <= 0) {
      ProfessionalToast.showError(context, message: 'Invalid amount configured for this method');
      return;
    }

    setState(() => _isSubmitting = true);

    final result = await _apiService.submitRedeemRequest(
      _selectedMethodId!,
      _accountController.text,
      amount,
    );

    setState(() => _isSubmitting = false);

    if (mounted) {
      if (result['success'] == true || result['status'] == true) {
        ProfessionalToast.showSuccess(context, message: 'Redeem request submitted!');
        Navigator.pop(context);
      } else {
        ProfessionalToast.showError(context, message: result['message'] ?? 'Failed to submit request');
      }
    }
  }

  Widget _buildGatewayShimmer() {
    return Padding(
      padding: const EdgeInsets.all(20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Shimmer.fromColors(
            baseColor: Colors.grey[900]!,
            highlightColor: Colors.grey[800]!,
            child: Container(
              width: 150,
              height: 24,
              color: Colors.white,
            ),
          ),
          const SizedBox(height: 12),
          SizedBox(
            height: 100,
            child: ListView.separated(
              scrollDirection: Axis.horizontal,
              itemCount: 4,
              separatorBuilder: (c, i) => const SizedBox(width: 12),
              itemBuilder: (context, index) {
                return Shimmer.fromColors(
                  baseColor: Colors.grey[900]!,
                  highlightColor: Colors.grey[800]!,
                  child: Container(
                    width: 100,
                    decoration: BoxDecoration(
                      color: Colors.black,
                      borderRadius: BorderRadius.circular(16),
                    ),
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildMethodShimmer() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Shimmer.fromColors(
          baseColor: Colors.grey[900]!,
          highlightColor: Colors.grey[800]!,
          child: Container(
            width: 150,
            height: 24,
            color: Colors.white,
          ),
        ),
        const SizedBox(height: 12),
        GridView.builder(
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: 2,
            childAspectRatio: 1.5,
            crossAxisSpacing: 12,
            mainAxisSpacing: 12,
          ),
          itemCount: 4,
          itemBuilder: (context, index) {
            return Shimmer.fromColors(
              baseColor: Colors.grey[900]!,
              highlightColor: Colors.grey[800]!,
              child: Container(
                decoration: BoxDecoration(
                  color: Colors.black,
                  borderRadius: BorderRadius.circular(16),
                ),
              ),
            );
          },
        ),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    final languageProvider = Provider.of<LanguageProvider>(context);

    return Scaffold(
      backgroundColor: AppTheme.darkBackgroundColor,
      bottomNavigationBar: const SafeArea(
        child: SizedBox(
          height: 50,
          child: UniversalBannerAd(),
        ),
      ),
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        title: Text(
          'Redeem Coins',
          style: GoogleFonts.poppins(color: Colors.white, fontWeight: FontWeight.bold),
        ),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_rounded, color: Colors.white),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: _isLoadingGateways
          ? _buildGatewayShimmer()
          : RefreshIndicator(
              onRefresh: _loadGateways,
              color: Colors.amber,
              backgroundColor: const Color(0xFF1F1B2E),
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                padding: const EdgeInsets.all(20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                  Text(
                    'Select Gateway',
                    style: GoogleFonts.poppins(
                      color: Colors.white70,
                      fontSize: 16,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                  const SizedBox(height: 12),
                  SizedBox(
                    height: 100,
                    child: ListView.separated(
                      scrollDirection: Axis.horizontal,
                      itemCount: _gateways.length,
                      separatorBuilder: (c, i) => const SizedBox(width: 12),
                      itemBuilder: (context, index) {
                        final gateway = _gateways[index];
                        final isSelected = gateway['id'] == _selectedGatewayId;
                        return GestureDetector(
                          onTap: () => _onGatewaySelected(gateway['id']),
                          child: Container(
                            width: 100,
                            decoration: BoxDecoration(
                              color: isSelected ? Colors.amber.withOpacity(0.2) : const Color(0xFF1F1B2E),
                              borderRadius: BorderRadius.circular(16),
                              border: Border.all(
                                color: isSelected ? Colors.amber : Colors.white.withOpacity(0.1),
                                width: 2,
                              ),
                            ),
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                if (gateway['image'] != null)
                                  Image.network(gateway['image'], height: 40, width: 40, errorBuilder: (_,__,___) => const Icon(Icons.payment, color: Colors.white))
                                else
                                  const Icon(Icons.account_balance_wallet_rounded, color: Colors.white, size: 32),
                                const SizedBox(height: 8),
                                Text(
                                  gateway['name'],
                                  style: GoogleFonts.poppins(
                                    color: Colors.white,
                                    fontSize: 12,
                                    fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                                  ),
                                  textAlign: TextAlign.center,
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ],
                            ),
                          ),
                        ).animate().scale(duration: 200.ms);
                      },
                    ),
                  ),

                  const SizedBox(height: 32),
                  
                  if (_isLoadingMethods)
                    _buildMethodShimmer()
                  else if (_methods.isNotEmpty)
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Select Method',
                          style: GoogleFonts.poppins(
                            color: Colors.white70,
                            fontSize: 16,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                        const SizedBox(height: 12),
                        GridView.builder(
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                            crossAxisCount: 2,
                            childAspectRatio: 1.5,
                            crossAxisSpacing: 12,
                            mainAxisSpacing: 12,
                          ),
                          itemCount: _methods.length,
                          itemBuilder: (context, index) {
                            final method = _methods[index];
                            final isSelected = method['id'] == _selectedMethodId;
                            return GestureDetector(
                              onTap: () => setState(() => _selectedMethodId = method['id']),
                              child: Container(
                                decoration: BoxDecoration(
                                  color: isSelected ? Colors.blueAccent.withOpacity(0.2) : const Color(0xFF1F1B2E),
                                  borderRadius: BorderRadius.circular(16),
                                  border: Border.all(
                                    color: isSelected ? Colors.blueAccent : Colors.white.withOpacity(0.1),
                                    width: 2,
                                  ),
                                ),
                                padding: const EdgeInsets.all(12),
                                child: Column(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    Text(
                                      method['name'],
                                      style: GoogleFonts.poppins(
                                        color: Colors.white,
                                        fontWeight: FontWeight.bold,
                                        fontSize: 14,
                                      ),
                                      textAlign: TextAlign.center,
                                    ),
                                    const SizedBox(height: 4),
                                    Text(
                                      'Min: ${method['amount'] ?? '0'} ${method['currency'] ?? ''}',
                                      style: GoogleFonts.poppins(
                                        color: Colors.white54,
                                        fontSize: 12,
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ).animate().fadeIn(delay: (50 * index).ms);
                          },
                        ),

                        if (_selectedMethodId != null) ...[
                          const SizedBox(height: 32),
                          Text(
                            'Withdrawal Details',
                            style: GoogleFonts.poppins(
                              color: Colors.white70,
                              fontSize: 16,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                          const SizedBox(height: 16),
                          _buildTextField(
                            controller: _accountController,
                            label: 'Account Number / Email',
                            icon: Icons.account_circle_outlined,
                          ),
                          // Amount field removed as it's now handled by the selected method's predefined amount
                          const SizedBox(height: 32),
                          SizedBox(
                            width: double.infinity,
                            height: 56,
                            child: ElevatedButton(
                              onPressed: _isSubmitting ? null : _submitRedeem,
                              style: ElevatedButton.styleFrom(
                                backgroundColor: Colors.amber,
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(16),
                                ),
                                elevation: 8,
                                shadowColor: Colors.amber.withOpacity(0.4),
                              ),
                              child: _isSubmitting
                                  ? const CircularProgressIndicator(color: Colors.black)
                                  : Text(
                                      'Redeem Now',
                                      style: GoogleFonts.poppins(
                                        color: Colors.black,
                                        fontSize: 18,
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                            ),
                          ),
                        ],
                      ],
                    )
                  else
                    Center(
                      child: Text(
                        'No methods available for this gateway',
                        style: GoogleFonts.poppins(color: Colors.white54),
                      ),
                    ),
                ],
              ),
            ),
          ),
    );
  }

  Widget _buildTextField({
    required TextEditingController controller,
    required String label,
    required IconData icon,
    TextInputType? keyboardType,
  }) {
    return Container(
      decoration: BoxDecoration(
        color: const Color(0xFF1F1B2E),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.white.withOpacity(0.1)),
      ),
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
      child: TextField(
        controller: controller,
        keyboardType: keyboardType,
        style: GoogleFonts.poppins(color: Colors.white),
        decoration: InputDecoration(
          border: InputBorder.none,
          icon: Icon(icon, color: Colors.white54),
          labelText: label,
          labelStyle: GoogleFonts.poppins(color: Colors.white54),
        ),
      ),
    );
  }
}
