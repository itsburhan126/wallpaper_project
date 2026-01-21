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
    try {
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
    } catch (e) {
      debugPrint("❌ CRITICAL ERROR in showAdWithFallback: $e");
      return false;
    }
  }

  static Future<bool> _tryAdMob(BuildContext context, VoidCallback onSuccess) async {
    final adService = GoogleAdService();

    // 1. FAST PATH: Check if Rewarded is ready
    if (adService.isRewardedAdReady()) {
      debugPrint("🔹 AdMob Rewarded Ad is READY. Showing immediately.");
      bool success = await adService.showRewardedAd(
        context,
        onReward: (amount) {},
        onFailure: () => debugPrint("❌ AdMob Rewarded Ad Failed to Show"),
      );
      if (success) {
        onSuccess();
        return true;
      }
    }

    // 2. FAST PATH: Check if Interstitial is ready (Skip loading Rewarded)
    if (adService.isInterstitialAdReady()) {
      debugPrint("🔹 AdMob Interstitial Ad is READY. Skipping Rewarded Load.");
      bool success = await adService.showInterstitialAd(
        context, 
        onAdDismissed: () {}
      );
      if (success) {
        onSuccess();
        return true;
      }
    }

    // 3. SLOW PATH: Neither ready, try loading Rewarded
    debugPrint("🔹 No ads ready. Attempting to load Rewarded Ad...");
    bool rewardedSuccess = await adService.showRewardedAd(
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

    // 4. SLOW PATH: Fallback to Interstitial Ad if Rewarded failed
    debugPrint("🔹 Rewarded Ad failed/not ready. Showing AdMob Interstitial Ad as Fallback");
    bool interstitialSuccess = await adService.showInterstitialAd(
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
