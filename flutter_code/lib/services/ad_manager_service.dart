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
    // If fallback order is empty, default to AdMob
    final priorities = fallbackOrder.isNotEmpty ? fallbackOrder : ['admob'];

    debugPrint("🚀 Starting Ad Sequence with Priorities: $priorities");

    for (int i = 0; i < priorities.length; i++) {
      final network = priorities[i].toLowerCase();
      final priorityIndex = i + 1;
      
      debugPrint("--------------------------------------------------");
      debugPrint("🔹 Trying Priority $priorityIndex: ${network.toUpperCase()}");
      
      bool isSuccess = false;
      
      if (network.contains('admob')) {
         isSuccess = await _tryAdMob(context, onSuccess);
      } else {
         debugPrint("⚠️ Network $network not implemented yet. Skipping.");
      }

      if (isSuccess) {
        debugPrint("✅ Ad Success with Priority $priorityIndex ($network)");
        debugPrint("--------------------------------------------------");
        return true;
      } else {
        debugPrint("❌ Failed Priority $priorityIndex ($network). Checking next...");
      }
    }

    debugPrint("🔴 All Ad Priorities Failed.");
    debugPrint("--------------------------------------------------");
    return false;
  }

  static Future<bool> _tryAdMob(BuildContext context, VoidCallback onSuccess) async {
    final completer = Completer<bool>();
    
    // 1. Try Rewarded Ad
    if (GoogleAdService().isRewardedAdReady()) {
      debugPrint("🔹 Showing AdMob Rewarded Ad");
      await GoogleAdService().showRewardedAd(
        context,
        onReward: (amount) {
          onSuccess();
          if (!completer.isCompleted) completer.complete(true);
        },
        onFailure: () {
          // If Rewarded fails, we can fall through or just return false
          // But wait, showRewardedAd returns logic. 
          // If failure happens, we might want to try Interstitial?
          // For now, let's keep it simple. If show fails, we fail.
          // But if we want robust fallback:
          if (!completer.isCompleted) completer.complete(false);
        },
      );
    } 
    // 2. Fallback to Interstitial Ad if Rewarded is not ready (or failed to load)
    else if (GoogleAdService().isInterstitialAdReady()) {
       debugPrint("🔹 Rewarded Ad not ready. Showing AdMob Interstitial Ad as Fallback");
       await GoogleAdService().showInterstitialAd(
         context, 
         onAdDismissed: () {
            // For Interstitial, we treat dismissal as "Success" for the purpose of the reward flow
            // assuming the user watched it.
            onSuccess();
            if (!completer.isCompleted) completer.complete(true);
         }
       );
    }
    else {
      debugPrint("❌ No AdMob Ads Ready (Rewarded or Interstitial)");
      if (!completer.isCompleted) completer.complete(false);
    }

    // Ensure completer is completed
    if (!completer.isCompleted) {
      completer.complete(false);
    }

    return completer.future;
  }
}
