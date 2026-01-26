import 'package:flutter/material.dart';
import 'package:facebook_audience_network/facebook_audience_network.dart';
import 'package:provider/provider.dart';
import '../providers/ad_provider.dart';
import 'dart:async';

class FacebookAdService {
  static final FacebookAdService _instance = FacebookAdService._internal();
  factory FacebookAdService() => _instance;
  FacebookAdService._internal();

  bool _isInterstitialLoaded = false;
  bool _isRewardedLoaded = false;
  bool _isInitialized = false;

  // Callbacks storage
  VoidCallback? _onInterstitialDismissed;
  Function(int)? _onReward;
  VoidCallback? _onRewardedDismissed;
  VoidCallback? _onRewardedFailed;

  Future<void> init() async {
    if (_isInitialized) return;
    await FacebookAudienceNetwork.init();
    _isInitialized = true;
  }

  bool isInterstitialAdReady() {
    return _isInterstitialLoaded;
  }

  bool isRewardedAdReady() {
    return _isRewardedLoaded;
  }

  Future<bool> loadInterstitialAd(BuildContext context) async {
    await init();
    final adProvider = Provider.of<AdProvider>(context, listen: false);
    if (!adProvider.adsEnabled || adProvider.facebookInterstitialId.isEmpty) {
      return false;
    }

    final completer = Completer<bool>();

    FacebookInterstitialAd.loadInterstitialAd(
      placementId: adProvider.facebookInterstitialId,
      listener: (result, value) {
        if (result == InterstitialAdResult.LOADED) {
          print("✅ Facebook Interstitial Ad Loaded");
          _isInterstitialLoaded = true;
          if (!completer.isCompleted) completer.complete(true);
        } else if (result == InterstitialAdResult.DISMISSED) {
          _isInterstitialLoaded = false;
          _onInterstitialDismissed?.call();
        } else if (result == InterstitialAdResult.ERROR) {
          print("❌ Facebook Interstitial Ad Failed to Load: $value");
          _isInterstitialLoaded = false;
          if (!completer.isCompleted) completer.complete(false);
        }
      },
    );

    return completer.future;
  }

  Future<bool> showInterstitialAd(BuildContext context, {required VoidCallback onAdDismissed}) async {
    if (!_isInterstitialLoaded) {
      return false;
    }
    
    _onInterstitialDismissed = onAdDismissed;
    
    bool result = (await FacebookInterstitialAd.showInterstitialAd()) ?? false;
    if (!result) {
       // If show fails immediately
       return false;
    }
    return true;
  }

  Future<bool> loadRewardedAd(BuildContext context) async {
    await init();
    final adProvider = Provider.of<AdProvider>(context, listen: false);
    if (!adProvider.adsEnabled || adProvider.facebookRewardedId.isEmpty) {
       return false;
    }

    final completer = Completer<bool>();

    FacebookRewardedVideoAd.loadRewardedVideoAd(
      placementId: adProvider.facebookRewardedId,
      listener: (result, value) {
        if (result == RewardedVideoAdResult.LOADED) {
          print("✅ Facebook Rewarded Ad Loaded");
          _isRewardedLoaded = true;
          if (!completer.isCompleted) completer.complete(true);
        } else if (result == RewardedVideoAdResult.VIDEO_COMPLETE) {
           _onReward?.call(1);
        } else if (result == RewardedVideoAdResult.VIDEO_CLOSED) {
           _isRewardedLoaded = false;
           _onRewardedDismissed?.call();
        } else if (result == RewardedVideoAdResult.ERROR) {
           print("❌ Facebook Rewarded Ad Failed to Load: $value");
          _isRewardedLoaded = false;
          if (!completer.isCompleted) completer.complete(false);
          _onRewardedFailed?.call();
        }
      },
    );

    return completer.future;
  }

  Future<bool> showRewardedAd(BuildContext context, {required Function(int) onReward, required VoidCallback onFailure}) async {
    if (!_isRewardedLoaded) {
      onFailure();
      return false;
    }
    
    _onReward = onReward;
    _onRewardedFailed = onFailure;
    // We can also use onFailure for dismiss if we want, or just ignore dismiss here as it's not a failure per se.
    // Usually AdManager expects onSuccess when reward happens.
    
    bool result = await FacebookRewardedVideoAd.showRewardedVideoAd() ?? false;
    if (!result) {
        onFailure();
        return false;
    }
    return true;
  }
}
