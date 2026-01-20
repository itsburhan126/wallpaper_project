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
    // 1. Try Rewarded Ad (Loads if needed)
    debugPrint("🔹 Attempting AdMob Rewarded Ad");
    bool rewardedSuccess = await GoogleAdService().showRewardedAd(
      context,
      onReward: (amount) {
        // Reward tracked internally in GoogleAdService, returned as true
      },
      onFailure: () {
         debugPrint("❌ AdMob Rewarded Ad Failed to Show/Load");
      },
    );

    if (rewardedSuccess) {
       onSuccess();
       return true;
    }

    // 2. Fallback to Interstitial Ad if Rewarded failed
    debugPrint("🔹 Rewarded Ad failed/not ready. Showing AdMob Interstitial Ad as Fallback");
    bool interstitialSuccess = await GoogleAdService().showInterstitialAd(
      context, 
      onAdDismissed: () {
         // Interstitial dismissed
      }
    );

    if (interstitialSuccess) {
       onSuccess();
       return true;
    }

    debugPrint("❌ No AdMob Ads Ready (Rewarded or Interstitial)");
    return false;
  }
}
