import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:safe_device/safe_device.dart';
import 'package:connectivity_plus/connectivity_plus.dart';
import 'package:device_info_plus/device_info_plus.dart';
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'api_service.dart';
import '../providers/language_provider.dart';

import 'package:google_fonts/google_fonts.dart';

class SecurityService {
  final ApiService _apiService = ApiService();
  bool _isChecking = false;

  // Singleton
  static final SecurityService _instance = SecurityService._internal();
  factory SecurityService() => _instance;
  SecurityService._internal();

  Future<bool> checkSecurity(BuildContext context) async {
    if (_isChecking) return true;
    _isChecking = true;

    try {
      // 1. Fetch Settings
      final settings = await _apiService.getSecuritySettings();
      if (settings.isEmpty) {
        _isChecking = false;
        return true;
      }

      // 2. Check VPN
      if (settings['security_vpn_block'] == '1') {
        if (await _isVpnActive()) {
          if (context.mounted) _showBlockingDialog(context, 'VPN Detected', 'Please disable VPN to use this app.');
          _isChecking = false;
          return false;
        }
      }

      // 3. Check Root/Jailbreak
      if (settings['security_root_block'] == '1') {
        bool isJailBroken = await SafeDevice.isJailBroken;
        if (isJailBroken) {
           if (context.mounted) _showBlockingDialog(context, 'Rooted Device Detected', 'This app cannot be used on rooted or jailbroken devices for security reasons.');
           _isChecking = false;
           return false;
        }
      }

      // 4. Check Developer Mode
      if (settings['security_dev_mode_block'] == '1') {
        bool isDevMode = await SafeDevice.isDevelopmentModeEnable;
        if (isDevMode) {
           if (context.mounted) _showBlockingDialog(context, 'Developer Mode Detected', 'Please disable Developer Options to use this app.');
           _isChecking = false;
           return false;
        }
      }

      // 5. Check Emulator
      if (settings['security_emulator_block'] == '1') {
        bool isRealDevice = await SafeDevice.isRealDevice;
        if (!isRealDevice) {
           if (context.mounted) _showBlockingDialog(context, 'Emulator Detected', 'This app is designed for real devices only.');
           _isChecking = false;
           return false;
        }
      }

      // 6. One Device One Account (Optional - needs backend support for full enforcement)
      // This is a placeholder for future implementation where we compare device ID
      
    } catch (e) {
      print('Security check error: $e');
    } finally {
      _isChecking = false;
    }
    
    return true;
  }

  Future<bool> _isVpnActive() async {
    try {
      final connectivityResult = await Connectivity().checkConnectivity();
      // connectivity_plus 6.0+ returns List<ConnectivityResult>
      if (connectivityResult is List) {
        return connectivityResult.contains(ConnectivityResult.vpn);
      } else {
        // Fallback for older versions if any (though we installed 7.0.0)
        return connectivityResult == ConnectivityResult.vpn;
      }
    } catch (e) {
      print('VPN Check Error: $e');
      return false;
    }
  }

  void _showBlockingDialog(BuildContext context, String title, String message) {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (BuildContext context) {
        return PopScope(
          canPop: false,
          child: Dialog(
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
            elevation: 0,
            backgroundColor: Colors.transparent,
            child: Container(
              padding: const EdgeInsets.all(28.0),
              decoration: BoxDecoration(
                color: const Color(0xFF1E1E1E),
                borderRadius: BorderRadius.circular(24),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.5),
                    blurRadius: 24,
                    offset: const Offset(0, 8),
                  ),
                ],
              ),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Container(
                    width: 80,
                    height: 80,
                    decoration: BoxDecoration(
                      color: Colors.red.withOpacity(0.1),
                      shape: BoxShape.circle,
                      border: Border.all(color: Colors.red.withOpacity(0.5), width: 2),
                    ),
                    child: const Icon(
                      Icons.gpp_bad_rounded,
                      color: Colors.white,
                      size: 40,
                    ),
                  ),
                  const SizedBox(height: 24),
                  Text(
                    title,
                    style: GoogleFonts.poppins(
                      fontSize: 22,
                      fontWeight: FontWeight.w600,
                      color: Colors.white,
                      height: 1.2,
                    ),
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 12),
                  Text(
                    message,
                    style: GoogleFonts.poppins(
                      fontSize: 15,
                      color: Colors.white70,
                      height: 1.5,
                    ),
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 32),
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton(
                      onPressed: () {
                        if (Platform.isAndroid) {
                          SystemNavigator.pop();
                        } else if (Platform.isIOS) {
                          exit(0);
                        }
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.red.shade600,
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(16),
                        ),
                        elevation: 0,
                      ),
                      child: Text(
                        'Close App',
                        style: GoogleFonts.poppins(
                          fontSize: 16,
                          fontWeight: FontWeight.w600,
                          letterSpacing: 0.5,
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        );
      },
    );
  }
}
