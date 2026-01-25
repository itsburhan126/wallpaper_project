import 'dart:io';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:image_picker/image_picker.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import '../providers/app_provider.dart';
import '../providers/language_provider.dart';
import '../utils/app_theme.dart';
import '../widgets/toast/professional_toast.dart';

class EditProfileScreen extends StatefulWidget {
  const EditProfileScreen({super.key});

  @override
  State<EditProfileScreen> createState() => _EditProfileScreenState();
}

class _EditProfileScreenState extends State<EditProfileScreen> {
  final _formKey = GlobalKey<FormState>();
  late TextEditingController _nameController;
  late TextEditingController _emailController;
  
  // Password Change Controllers
  final _currentPasswordController = TextEditingController();
  final _newPasswordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();
  
  bool _isLoading = false;
  File? _selectedImage;
  bool _showPasswordSection = false;

  @override
  void initState() {
    super.initState();
    final provider = Provider.of<AppProvider>(context, listen: false);
    _nameController = TextEditingController(text: provider.userName);
    _emailController = TextEditingController(text: provider.userEmail);
  }

  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    _currentPasswordController.dispose();
    _newPasswordController.dispose();
    _confirmPasswordController.dispose();
    super.dispose();
  }

  Future<void> _pickImage() async {
    final picker = ImagePicker();
    final pickedFile = await picker.pickImage(source: ImageSource.gallery);

    if (pickedFile != null) {
      setState(() {
        _selectedImage = File(pickedFile.path);
      });
      _uploadImage();
    }
  }

  Future<void> _uploadImage() async {
    if (_selectedImage == null) return;
    
    setState(() => _isLoading = true);
    final provider = Provider.of<AppProvider>(context, listen: false);
    final langProvider = Provider.of<LanguageProvider>(context, listen: false);
    
    final result = await provider.updateAvatar(_selectedImage!);
    
    setState(() => _isLoading = false);
    
    if (result['success'] == true) {
      if (mounted) ProfessionalToast.showSuccess(context, message: langProvider.getText('profile_pic_updated'));
    } else {
      if (mounted) ProfessionalToast.showError(context, message: result['message'] ?? langProvider.getText('failed_update_pic'));
    }
  }

  Future<void> _saveProfile() async {
    if (!_formKey.currentState!.validate()) return;
    
    setState(() => _isLoading = true);
    final provider = Provider.of<AppProvider>(context, listen: false);
    final langProvider = Provider.of<LanguageProvider>(context, listen: false);
    
    // 1. Update Name
    if (_nameController.text != provider.userName) {
      final nameResult = await provider.updateProfile(_nameController.text);
      if (nameResult['success'] != true) {
        if (mounted) ProfessionalToast.showError(context, message: nameResult['message'] ?? langProvider.getText('failed_update_name'));
        setState(() => _isLoading = false);
        return;
      }
    }

    // 2. Change Password (if section visible and filled)
    if (_showPasswordSection && _currentPasswordController.text.isNotEmpty) {
      if (_newPasswordController.text != _confirmPasswordController.text) {
        if (mounted) ProfessionalToast.showError(context, message: langProvider.getText('passwords_do_not_match'));
        setState(() => _isLoading = false);
        return;
      }
      
      final passResult = await provider.changePassword(
        _currentPasswordController.text,
        _newPasswordController.text,
      );
      
      if (passResult['success'] == true) {
        if (mounted) ProfessionalToast.showSuccess(context, message: langProvider.getText('password_changed_success'));
        _currentPasswordController.clear();
        _newPasswordController.clear();
        _confirmPasswordController.clear();
        setState(() => _showPasswordSection = false);
      } else {
        if (mounted) ProfessionalToast.showError(context, message: passResult['message'] ?? langProvider.getText('failed_change_password'));
        setState(() => _isLoading = false);
        return;
      }
    }
    
    setState(() => _isLoading = false);
    if (mounted) {
      ProfessionalToast.showSuccess(context, message: langProvider.getText('profile_updated_success'));
      Navigator.pop(context);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Consumer2<AppProvider, LanguageProvider>(
      builder: (context, provider, langProvider, child) {
        return Scaffold(
          backgroundColor: AppTheme.darkBackgroundColor,
          appBar: AppBar(
            backgroundColor: Colors.transparent,
            elevation: 0,
            leading: IconButton(
              icon: Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.1),
                  shape: BoxShape.circle,
                ),
                child: const Icon(Icons.arrow_back_ios_new, size: 18, color: Colors.white),
              ),
              onPressed: () => Navigator.pop(context),
            ),
            title: Text(
              langProvider.getText('edit_profile'),
              style: GoogleFonts.plusJakartaSans(
                fontWeight: FontWeight.bold,
                color: Colors.white,
              ),
            ),
            centerTitle: true,
          ),
          extendBodyBehindAppBar: true,
          body: Stack(
            children: [
              // Background
              Container(decoration: AppTheme.backgroundDecoration),
              
              SingleChildScrollView(
                padding: const EdgeInsets.fromLTRB(20, 100, 20, 40),
                physics: const BouncingScrollPhysics(),
                child: Form(
                  key: _formKey,
                  child: Column(
                    children: [
                      _buildProfileImage(),
                      const SizedBox(height: 30),
                      _buildTextField(
                        controller: _nameController,
                        label: langProvider.getText('full_name'),
                        icon: Icons.person_outline_rounded,
                      ),
                      const SizedBox(height: 16),
                      _buildTextField(
                        controller: _emailController,
                        label: langProvider.getText('email_address'),
                        icon: Icons.email_outlined,
                        isReadOnly: true,
                      ),
                      const SizedBox(height: 30),
                      _buildPasswordSection(langProvider),
                      const SizedBox(height: 40),
                      _buildSaveButton(langProvider),
                    ],
                  ),
                ),
              ),
              
              if (_isLoading)
                Container(
                  color: Colors.black54,
                  child: const Center(
                    child: CircularProgressIndicator(color: Colors.indigoAccent),
                  ),
                ),
            ],
          ),
        );
      }
    );
  }

  Widget _buildProfileImage() {
    return Consumer<AppProvider>(
      builder: (context, provider, _) {
        return Stack(
          alignment: Alignment.center,
          children: [
            Container(
              width: 120,
              height: 120,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                border: Border.all(color: Colors.white, width: 3),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.3),
                    blurRadius: 20,
                    spreadRadius: 5,
                  ),
                ],
              ),
              child: ClipOval(
                child: _selectedImage != null
                    ? Image.file(_selectedImage!, fit: BoxFit.cover)
                    : (provider.userAvatar.isNotEmpty
                        ? Image.network(
                            provider.userAvatar,
                            fit: BoxFit.cover,
                            errorBuilder: (context, error, stackTrace) =>
                                Image.network("https://i.pravatar.cc/300", fit: BoxFit.cover),
                          )
                        : Image.network("https://i.pravatar.cc/300", fit: BoxFit.cover)),
              ),
            ),
            Positioned(
              bottom: 0,
              right: 0,
              child: GestureDetector(
                onTap: _pickImage,
                child: Container(
                  padding: const EdgeInsets.all(10),
                  decoration: const BoxDecoration(
                    color: Colors.indigoAccent,
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(Icons.camera_alt, color: Colors.white, size: 20),
                ),
              ),
            ),
          ],
        ).animate().scale(duration: 600.ms, curve: Curves.elasticOut);
      },
    );
  }

  Widget _buildTextField({
    required TextEditingController controller,
    required String label,
    required IconData icon,
    bool isPassword = false,
    bool isReadOnly = false,
    String? Function(String?)? validator,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: GoogleFonts.plusJakartaSans(
            color: Colors.white70,
            fontSize: 14,
            fontWeight: FontWeight.w500,
          ),
        ),
        const SizedBox(height: 8),
        Container(
          decoration: BoxDecoration(
            color: const Color(0xFF1F1B2E),
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: Colors.white.withOpacity(0.1)),
          ),
          child: TextFormField(
            controller: controller,
            obscureText: isPassword,
            readOnly: isReadOnly,
            style: GoogleFonts.plusJakartaSans(color: Colors.white),
            validator: validator,
            decoration: InputDecoration(
              prefixIcon: Icon(icon, color: Colors.white54, size: 20),
              border: InputBorder.none,
              contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
              hintStyle: GoogleFonts.plusJakartaSans(color: Colors.white24),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildPasswordSection(LanguageProvider langProvider) {
    return Column(
      children: [
        InkWell(
          onTap: () => setState(() => _showPasswordSection = !_showPasswordSection),
          borderRadius: BorderRadius.circular(12),
          child: Padding(
            padding: const EdgeInsets.symmetric(vertical: 8),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  langProvider.getText('change_password'),
                  style: GoogleFonts.plusJakartaSans(
                    color: Colors.white,
                    fontSize: 16,
                    fontWeight: FontWeight.w600,
                  ),
                ),
                Icon(
                  _showPasswordSection ? Icons.keyboard_arrow_up : Icons.keyboard_arrow_down,
                  color: Colors.white,
                ),
              ],
            ),
          ),
        ),
        if (_showPasswordSection) ...[
          const SizedBox(height: 16),
          _buildTextField(
            controller: _currentPasswordController,
            label: langProvider.getText('current_password'),
            icon: Icons.lock_outline,
            isPassword: true,
            validator: (val) {
              if (_showPasswordSection && (val == null || val.isEmpty)) {
                return langProvider.getText('enter_current_password');
              }
              return null;
            },
          ),
          const SizedBox(height: 16),
          _buildTextField(
            controller: _newPasswordController,
            label: langProvider.getText('new_password'),
            icon: Icons.lock_reset,
            isPassword: true,
            validator: (val) {
              if (_showPasswordSection && (val == null || val.length < 6)) {
                return langProvider.getText('password_min_length');
              }
              return null;
            },
          ),
          const SizedBox(height: 16),
          _buildTextField(
            controller: _confirmPasswordController,
            label: langProvider.getText('confirm_new_password'),
            icon: Icons.check_circle_outline,
            isPassword: true,
            validator: (val) {
              if (_showPasswordSection && val != _newPasswordController.text) {
                return langProvider.getText('passwords_do_not_match');
              }
              return null;
            },
          ),
        ],
      ],
    ).animate().fadeIn();
  }

  Widget _buildSaveButton(LanguageProvider langProvider) {
    return Container(
      width: double.infinity,
      height: 56,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(16),
        gradient: const LinearGradient(
          colors: [Colors.indigoAccent, Colors.purpleAccent],
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.indigoAccent.withOpacity(0.4),
            blurRadius: 20,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: _saveProfile,
          borderRadius: BorderRadius.circular(16),
          child: Center(
            child: Text(
              langProvider.getText('save_changes'),
              style: GoogleFonts.plusJakartaSans(
                color: Colors.white,
                fontSize: 16,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ),
      ),
    ).animate().slideY(begin: 1, end: 0, delay: 200.ms);
  }
}
