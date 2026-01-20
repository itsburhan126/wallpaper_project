import 'package:flutter/material.dart';
import 'dart:async';
import 'google_ad_service.dart';

/// Ad Manager to handle Fallback Logic
class AdManager {
  static Future<bool> showAdWithFallback(
    BuildContext context, 
    List<String> fallbackOrder, 
    VoidCallback onSuccess
  ) async {
    // Only AdMob is implemented for now as per instruction
    debugPrint("🔵 Trying Ad Network: AdMob (Priority 1)");
    
    final completer = Completer<bool>();
    
    await GoogleAdService().showRewardedAd(
      context,
      onReward: (amount) {
        debugPrint("🟢 Ad Success: AdMob");
        onSuccess();
        if (!completer.isCompleted) completer.complete(true);
      },
      onFailure: () {
        debugPrint("🔴 Ad Failed: AdMob");
        if (!completer.isCompleted) completer.complete(false);
      },
    );

    return completer.future;
  }
}
