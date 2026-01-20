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
  Future<bool>? _rewardedAdLoadFuture;
  DateTime? _rewardedAdLoadStartTime;
  Future<bool>? _interstitialAdLoadFuture;

  bool isRewardedAdReady() {
    return _rewardedAd != null;
  }

  bool isInterstitialAdReady() {
    return _interstitialAd != null;
  }

  Future<bool> loadRewardedAd(BuildContext context) async {
    final adProvider = Provider.of<AdProvider>(context, listen: false);
    if (!adProvider.adsEnabled || adProvider.admobRewardedId == null) return false;

    // Check if a load is already in progress
    if (_rewardedAdLoadFuture != null) {
        // If it's been loading for more than 20 seconds, assume it's stuck and force reload
        if (_rewardedAdLoadStartTime != null && 
            DateTime.now().difference(_rewardedAdLoadStartTime!) > const Duration(seconds: 20)) {
            print("⚠️ Previous ad load seems stuck (>20s). Resetting...");
            _rewardedAdLoadFuture = null;
        } else {
            return _rewardedAdLoadFuture!;
        }
    }

    print("🎬 Loading Rewarded Ad: ${adProvider.admobRewardedId}");

    final completer = Completer<bool>();
    _rewardedAdLoadFuture = completer.future;
    _rewardedAdLoadStartTime = DateTime.now();

    // Internal Safety Timeout to prevent stuck loading state
    Timer(const Duration(seconds: 15), () {
      if (!completer.isCompleted) {
        print("⚠️ Rewarded Ad Load Timed Out (Internal 15s limit) - Resetting state");
        _rewardedAdLoadFuture = null; // Allow new load attempts
        completer.complete(false);
      }
    });

    await RewardedAd.load(
      adUnitId: adProvider.admobRewardedId!,
      request: const AdRequest(),
      rewardedAdLoadCallback: RewardedAdLoadCallback(
        onAdLoaded: (ad) {
          print("✅ Rewarded Ad Loaded");
          _rewardedAd = ad;
          _rewardedAdLoadFuture = null;
          if (!completer.isCompleted) completer.complete(true);
        },
        onAdFailedToLoad: (error) {
          print("❌ Rewarded Ad Failed to Load: $error");
          _rewardedAd = null;
          _rewardedAdLoadFuture = null;
          if (!completer.isCompleted) completer.complete(false);
        },
      ),
    );
    
    return completer.future;
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
      bool loaded = await loadRewardedAd(context);
      if (!loaded || _rewardedAd == null) {
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
  Future<bool> loadInterstitialAd(BuildContext context) async {
    final adProvider = Provider.of<AdProvider>(context, listen: false);
    if (!adProvider.interstitialEnabled || adProvider.admobInterstitialId == null) return false;

    if (_interstitialAdLoadFuture != null) return _interstitialAdLoadFuture!;

    print("🎬 Loading Interstitial Ad: ${adProvider.admobInterstitialId}");

    final completer = Completer<bool>();
    _interstitialAdLoadFuture = completer.future;

    await InterstitialAd.load(
      adUnitId: adProvider.admobInterstitialId!,
      request: const AdRequest(),
      adLoadCallback: InterstitialAdLoadCallback(
        onAdLoaded: (ad) {
          print("✅ Interstitial Ad Loaded");
          _interstitialAd = ad;
          _interstitialAdLoadFuture = null;
          if (!completer.isCompleted) completer.complete(true);
        },
        onAdFailedToLoad: (error) {
          print("❌ Interstitial Ad Failed to Load: $error");
          _interstitialAd = null;
          _interstitialAdLoadFuture = null;
          if (!completer.isCompleted) completer.complete(false);
        },
      ),
    );
    
    return completer.future;
  }

  Future<bool> showInterstitialAd(BuildContext context, {VoidCallback? onAdDismissed}) async {
    final adProvider = Provider.of<AdProvider>(context, listen: false);

    if (!adProvider.interstitialEnabled) return false;

    if (_interstitialAd == null) {
      bool loaded = await loadInterstitialAd(context);
      if (!loaded || _interstitialAd == null) return false;
    }

    final completer = Completer<bool>();

    _interstitialAd!.fullScreenContentCallback = FullScreenContentCallback(
      onAdDismissedFullScreenContent: (ad) {
        print("🛑 Interstitial Ad dismissed");
        ad.dispose();
        _interstitialAd = null;
        loadInterstitialAd(context);
        if (onAdDismissed != null) onAdDismissed();
        if (!completer.isCompleted) completer.complete(true);
      },
      onAdFailedToShowFullScreenContent: (ad, error) {
        print("❌ Interstitial Ad failed to show: $error");
        ad.dispose();
        _interstitialAd = null;
        loadInterstitialAd(context);
        if (!completer.isCompleted) completer.complete(false);
      },
      onAdShowedFullScreenContent: (ad) {
        print("🎬 Interstitial Ad showed fullscreen");
      },
    );

    _interstitialAd!.show();
    return completer.future;
  }
}
