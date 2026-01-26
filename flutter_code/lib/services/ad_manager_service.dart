import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/ad_provider.dart';
import 'dart:async';
import 'google_ad_service.dart';
import 'facebook_ad_service.dart';
import 'unity_ad_service.dart';

/// Ad Manager to handle Fallback Logic
class AdManager {
  
  static Future<void> preloadAds(BuildContext context) async {
    final adProvider = Provider.of<AdProvider>(context, listen: false);
    if (!adProvider.adsEnabled) return;

    debugPrint("🚀 AdManager: Preloading all configured ads...");

    // 1. Initialize Unity (if needed)
    if (adProvider.unityGameId.isNotEmpty) {
      UnityAdService().init(context);
    }

    // 2. Load AdMob (if IDs exist)
    if (adProvider.admobRewardedId.isNotEmpty) {
       GoogleAdService().loadRewardedAd(context);
    }
    if (adProvider.admobInterstitialId.isNotEmpty) {
       GoogleAdService().loadInterstitialAd(context);
    }

    // 3. Load Facebook (if IDs exist)
    if (adProvider.facebookRewardedId.isNotEmpty) {
       FacebookAdService().loadRewardedAd(context);
    }
    if (adProvider.facebookInterstitialId.isNotEmpty) {
       FacebookAdService().loadInterstitialAd(context);
    }

    // 4. Load Unity (if IDs exist)
    if (adProvider.unityRewardedId.isNotEmpty) {
       UnityAdService().loadAd(context, adProvider.unityRewardedId);
    }
    if (adProvider.unityInterstitialId.isNotEmpty) {
       UnityAdService().loadAd(context, adProvider.unityInterstitialId);
    }
    // Note: UnityAdService loadAd is generic, we can load interstitial too if we have the ID
    // But currently UnityAdService might not distinguish types, just placement IDs.
    // If we have an interstitial placement ID, we should load it too.
    // Assuming we might use it in fallback logic later if implemented.
  }

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
        } else if (network.contains('facebook')) {
           isSuccess = await _tryFacebook(context, onSuccess);
        } else if (network.contains('unity')) {
           isSuccess = await _tryUnity(context, onSuccess);
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
      onAdDismissed: () {}
    );
    
    if (interstitialSuccess) {
       onSuccess();
       return true;
    }

    return false;
  }

  static Future<bool> _tryFacebook(BuildContext context, VoidCallback onSuccess) async {
    final adService = FacebookAdService();

    // 1. Check Rewarded
    if (adService.isRewardedAdReady()) {
      debugPrint("🔹 Facebook Rewarded Ad is READY.");
      bool success = await adService.showRewardedAd(
        context,
        onReward: (amount) {},
        onFailure: () {},
      );
      if (success) {
        onSuccess();
        return true;
      }
    }

    // 2. Check Interstitial
    if (adService.isInterstitialAdReady()) {
      debugPrint("🔹 Facebook Interstitial Ad is READY.");
      bool success = await adService.showInterstitialAd(
        context,
        onAdDismissed: () {}
      );
      if (success) {
        onSuccess();
        return true;
      }
    }

    // 3. Load & Show Rewarded
    debugPrint("🔹 Loading Facebook Rewarded Ad...");
    await adService.loadRewardedAd(context);
    if (adService.isRewardedAdReady()) {
       bool success = await adService.showRewardedAd(
         context,
         onReward: (amount) {},
         onFailure: () {},
       );
       if (success) {
         onSuccess();
         return true;
       }
    }

    // 4. Load & Show Interstitial
    debugPrint("🔹 Loading Facebook Interstitial Ad...");
    await adService.loadInterstitialAd(context);
    if (adService.isInterstitialAdReady()) {
       bool success = await adService.showInterstitialAd(
         context,
         onAdDismissed: () {}
       );
       if (success) {
         onSuccess();
         return true;
       }
    }
    
    return false;
  }

  static Future<bool> _tryUnity(BuildContext context, VoidCallback onSuccess) async {
    final adService = UnityAdService();
    final adProvider = Provider.of<AdProvider>(context, listen: false);

    // Unity needs specific placement IDs. We'll try Rewarded then Interstitial.
    
    // 1. Try Rewarded
    String rewardedId = adProvider.unityRewardedId;
    if (rewardedId.isNotEmpty) {
      if (adService.isAdReady(rewardedId)) {
         debugPrint("🔹 Unity Rewarded Ad is READY.");
         bool success = await adService.showAd(
            context, 
            rewardedId,
            onReward: (amount) {},
            onFailed: () {}
         );
         if (success) {
           onSuccess();
           return true;
         }
      } else {
        debugPrint("🔹 Loading Unity Rewarded Ad...");
        await adService.loadAd(context, rewardedId);
        if (adService.isAdReady(rewardedId)) {
            bool success = await adService.showAd(
                context, 
                rewardedId,
                onReward: (amount) {},
                onFailed: () {}
            );
            if (success) {
              onSuccess();
              return true;
            }
        }
      }
    }

    // 2. Try Interstitial
    String interstitialId = adProvider.unityInterstitialId;
    if (interstitialId.isNotEmpty) {
        if (adService.isAdReady(interstitialId)) {
           debugPrint("🔹 Unity Interstitial Ad is READY.");
           bool success = await adService.showAd(
              context, 
              interstitialId,
              onDismissed: () {},
              onFailed: () {}
           );
           if (success) {
             onSuccess();
             return true;
           }
        } else {
           debugPrint("🔹 Loading Unity Interstitial Ad...");
           await adService.loadAd(context, interstitialId);
           if (adService.isAdReady(interstitialId)) {
              bool success = await adService.showAd(
                  context, 
                  interstitialId,
                  onDismissed: () {},
                  onFailed: () {}
              );
              if (success) {
                onSuccess();
                return true;
              }
           }
        }
    }

    return false;
  }
}
