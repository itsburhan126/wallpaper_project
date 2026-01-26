import 'package:flutter/material.dart';
import 'package:unity_ads_plugin/unity_ads_plugin.dart';
import 'package:provider/provider.dart';
import '../providers/ad_provider.dart';
import 'dart:async';

class UnityAdService {
  static final UnityAdService _instance = UnityAdService._internal();
  factory UnityAdService() => _instance;
  UnityAdService._internal();

  bool _isInitialized = false;
  final Map<String, bool> _loadedAds = {};

  Future<void> init(BuildContext context) async {
    if (_isInitialized) return;
    
    final adProvider = Provider.of<AdProvider>(context, listen: false);
    if (adProvider.unityGameId.isEmpty) return;

    await UnityAds.init(
      gameId: adProvider.unityGameId,
      onComplete: () {
        debugPrint('✅ Unity Ads Initialized');
        _isInitialized = true;
      },
      onFailed: (error, message) => debugPrint('❌ Unity Ads Init Failed: $error $message'),
    );
  }

  bool isAdReady(String placementId) {
    return _loadedAds[placementId] ?? false;
  }

  Future<bool> loadAd(BuildContext context, String placementId) async {
    await init(context);
    
    if (placementId.isEmpty) return false;

    final completer = Completer<bool>();

    await UnityAds.load(
      placementId: placementId,
      onComplete: (placementId) {
        debugPrint('✅ Unity Ad Loaded: $placementId');
        _loadedAds[placementId] = true;
        if (!completer.isCompleted) completer.complete(true);
      },
      onFailed: (placementId, error, message) {
        debugPrint('❌ Unity Ad Load Failed: $placementId, $error, $message');
        _loadedAds[placementId] = false;
        if (!completer.isCompleted) completer.complete(false);
      },
    );

    return completer.future;
  }

  Future<bool> showAd(BuildContext context, String placementId, {
    Function(dynamic)? onReward, 
    VoidCallback? onDismissed,
    VoidCallback? onFailed
  }) async {
    if (!_loadedAds.containsKey(placementId) || !_loadedAds[placementId]!) {
       // Try loading if not ready?
       // For now, fail.
       debugPrint("❌ Unity Ad not ready: $placementId");
       if (onFailed != null) onFailed();
       return false;
    }

    await UnityAds.showVideoAd(
      placementId: placementId,
      onStart: (placementId) => debugPrint('🎬 Unity Ad Started: $placementId'),
      onClick: (placementId) => debugPrint('🖱️ Unity Ad Clicked: $placementId'),
      onSkipped: (placementId) {
         debugPrint('⏭️ Unity Ad Skipped: $placementId');
         _loadedAds[placementId] = false;
         if (onDismissed != null) onDismissed();
      },
      onComplete: (placementId) {
         debugPrint('✅ Unity Ad Completed: $placementId');
         _loadedAds[placementId] = false;
         if (onReward != null) onReward(null);
         if (onDismissed != null) onDismissed();
      },
      onFailed: (placementId, error, message) {
        debugPrint('❌ Unity Ad Failed: $placementId, $error, $message');
        _loadedAds[placementId] = false;
        if (onFailed != null) onFailed();
      },
    );
    
    return true;
  }
}
