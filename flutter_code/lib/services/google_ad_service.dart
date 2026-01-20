import 'package:flutter/material.dart';
import 'package:google_mobile_ads/google_mobile_ads.dart';
import 'package:provider/provider.dart';
import '../providers/ad_provider.dart';
import 'dart:async';

class GoogleAdService {
  static final GoogleAdService _instance = GoogleAdService._internal();
  factory GoogleAdService() => _instance;
  GoogleAdService._internal();

  RewardedAd? _rewardedAd;
  InterstitialAd? _interstitialAd;
  bool _isRewardedLoading = false;
  bool _isInterstitialLoading = false;

  Future<void> loadRewardedAd(BuildContext context) async {
    final adProvider = Provider.of<AdProvider>(context, listen: false);
    if (!adProvider.adsEnabled || adProvider.admobRewardedId == null || _isRewardedLoading) return;

    _isRewardedLoading = true;
    print("🎬 Loading Rewarded Ad: ${adProvider.admobRewardedId}");

    await RewardedAd.load(
      adUnitId: adProvider.admobRewardedId!,
      request: const AdRequest(),
      rewardedAdLoadCallback: RewardedAdLoadCallback(
        onAdLoaded: (ad) {
          print("✅ Rewarded Ad Loaded");
          _rewardedAd = ad;
          _isRewardedLoading = false;
        },
        onAdFailedToLoad: (error) {
          print("❌ Rewarded Ad Failed to Load: $error");
          _rewardedAd = null;
          _isRewardedLoading = false;
        },
      ),
    );
  }

  Future<bool> showRewardedAd(BuildContext context, {required Function(int) onReward, required VoidCallback onFailure}) async {
    final adProvider = Provider.of<AdProvider>(context, listen: false);
    
    // Check if only AdMob is allowed
    if (adProvider.adProvider != 'admob_only' && adProvider.adProvider != 'both') {
      print("⚠️ AdMob is disabled by admin settings (Provider: ${adProvider.adProvider})");
      onFailure();
      return false;
    }

    if (_rewardedAd == null) {
      print("⚠️ Rewarded Ad not ready, attempting to load...");
      await loadRewardedAd(context);
      if (_rewardedAd == null) {
        onFailure();
        return false;
      }
    }

    final completer = Completer<bool>();

    _rewardedAd!.fullScreenContentCallback = FullScreenContentCallback(
      onAdShowedFullScreenContent: (ad) => print("🎬 Ad showed fullscreen"),
      onAdDismissedFullScreenContent: (ad) {
        print("🛑 Ad dismissed");
        ad.dispose();
        _rewardedAd = null;
        loadRewardedAd(context); // Preload next ad
        if (!completer.isCompleted) completer.complete(false);
      },
      onAdFailedToShowFullScreenContent: (ad, error) {
        print("❌ Ad failed to show: $error");
        ad.dispose();
        _rewardedAd = null;
        loadRewardedAd(context);
        onFailure();
        if (!completer.isCompleted) completer.complete(false);
      },
    );

    _rewardedAd!.show(onUserEarnedReward: (ad, reward) {
      print("💰 User earned reward: ${reward.amount} ${reward.type}");
      onReward(reward.amount.toInt());
      if (!completer.isCompleted) completer.complete(true);
    });

    return completer.future;
  }

  // Interstitial Ads
  Future<void> loadInterstitialAd(BuildContext context) async {
    final adProvider = Provider.of<AdProvider>(context, listen: false);
    if (!adProvider.interstitialEnabled || adProvider.admobInterstitialId == null || _isInterstitialLoading) return;

    _isInterstitialLoading = true;
    print("🎬 Loading Interstitial Ad: ${adProvider.admobInterstitialId}");

    await InterstitialAd.load(
      adUnitId: adProvider.admobInterstitialId!,
      request: const AdRequest(),
      adLoadCallback: InterstitialAdLoadCallback(
        onAdLoaded: (ad) {
          print("✅ Interstitial Ad Loaded");
          _interstitialAd = ad;
          _isInterstitialLoading = false;
        },
        onAdFailedToLoad: (error) {
          print("❌ Interstitial Ad Failed to Load: $error");
          _interstitialAd = null;
          _isInterstitialLoading = false;
        },
      ),
    );
  }

  Future<void> showInterstitialAd(BuildContext context) async {
    final adProvider = Provider.of<AdProvider>(context, listen: false);

    if (!adProvider.interstitialEnabled) return;

    if (_interstitialAd == null) {
      await loadInterstitialAd(context);
      if (_interstitialAd == null) return;
    }

    _interstitialAd!.fullScreenContentCallback = FullScreenContentCallback(
      onAdDismissedFullScreenContent: (ad) {
        ad.dispose();
        _interstitialAd = null;
        loadInterstitialAd(context);
      },
      onAdFailedToShowFullScreenContent: (ad, error) {
        ad.dispose();
        _interstitialAd = null;
        loadInterstitialAd(context);
      },
    );

    _interstitialAd!.show();
  }
}
