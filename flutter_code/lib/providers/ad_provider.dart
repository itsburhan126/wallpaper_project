import 'package:flutter/foundation.dart';
import 'dart:io';
import '../services/api_service.dart';

class AdProvider with ChangeNotifier {
  final ApiService _apiService = ApiService();

  bool _isLoading = true;
  bool get isLoading => _isLoading;

  // General Ad Settings
  bool _adsEnabled = true;
  bool _bannerEnabled = true;
  bool _interstitialEnabled = true;
  bool _nativeEnabled = true;
  String _adProvider = 'admob_only'; // admob_only, facebook_only
  
  String _nativeAdNetwork = 'admob';
  String _bannerAdNetwork = 'admob';
  String _interstitialAdNetwork = 'admob';

  // Page Specific Settings
  String _homeBannerNetwork = 'global';
  String _homeNativeNetwork = 'global';
  String _detailsBannerNetwork = 'global';
  String _detailsNativeNetwork = 'global';
  String _categoryBannerNetwork = 'global';
  String _categoryNativeNetwork = 'global';
  String _shortsNativeNetwork = 'global';
  int _shortsAdInterval = 5;
  int _homeAdInterval = 8;

  // AdMob IDs
  String _admobAndroidAppId = '';
  String _admobAndroidBannerId = '';
  String _admobAndroidInterstitialId = '';
  String _admobAndroidRewardedId = '';
  String _admobAndroidNativeId = '';
  
  String _admobIosAppId = '';
  String _admobIosBannerId = '';
  String _admobIosInterstitialId = '';
  String _admobIosRewardedId = '';
  String _admobIosNativeId = '';

  // Facebook IDs
  String _facebookAndroidBannerId = '';
  String _facebookAndroidInterstitialId = '';
  String _facebookAndroidNativeId = '';
  String _facebookAndroidRewardedId = '';
  
  String _facebookIosBannerId = '';
  String _facebookIosInterstitialId = '';
  String _facebookIosNativeId = '';
  String _facebookIosRewardedId = '';

  // Unity IDs
  String _unityAndroidGameId = '';
  String _unityAndroidBannerId = '';
  String _unityAndroidInterstitialId = '';
  String _unityAndroidRewardedId = '';
  
  String _unityIosGameId = '';
  String _unityIosBannerId = '';
  String _unityIosInterstitialId = '';
  String _unityIosRewardedId = '';

  String _pubscaleAppId = '';
  String _pubscaleApiKey = '';

  // Getters
  bool get adsEnabled => _adsEnabled;
  bool get bannerEnabled => _adsEnabled && _bannerEnabled;
  bool get interstitialEnabled => _adsEnabled && _interstitialEnabled;
  bool get nativeEnabled => _adsEnabled && _nativeEnabled;
  String get adProvider => _adProvider;
  
  String get nativeAdNetwork => _nativeAdNetwork;
  String get bannerAdNetwork => _bannerAdNetwork;
  String get interstitialAdNetwork => _interstitialAdNetwork;
  int get shortsAdInterval => _shortsAdInterval;
  int get homeAdInterval => _homeAdInterval;

  String get admobAppId => Platform.isIOS ? _admobIosAppId : _admobAndroidAppId;
  String get admobBannerId => Platform.isIOS ? _admobIosBannerId : _admobAndroidBannerId;
  String get admobInterstitialId => Platform.isIOS ? _admobIosInterstitialId : _admobAndroidInterstitialId;
  String get admobRewardedId => Platform.isIOS ? _admobIosRewardedId : _admobAndroidRewardedId;
  String get admobNativeId => Platform.isIOS ? _admobIosNativeId : _admobAndroidNativeId;

  // Facebook Getters
  String get facebookBannerId => Platform.isIOS ? _facebookIosBannerId : _facebookAndroidBannerId;
  String get facebookInterstitialId => Platform.isIOS ? _facebookIosInterstitialId : _facebookAndroidInterstitialId;
  String get facebookRewardedId => Platform.isIOS ? _facebookIosRewardedId : _facebookAndroidRewardedId;
  String get facebookNativeId => Platform.isIOS ? _facebookIosNativeId : _facebookAndroidNativeId;

  // Unity Getters
  String get unityGameId => Platform.isIOS ? _unityIosGameId : _unityAndroidGameId;
  String get unityBannerId => Platform.isIOS ? _unityIosBannerId : _unityAndroidBannerId;
  String get unityInterstitialId => Platform.isIOS ? _unityIosInterstitialId : _unityAndroidInterstitialId;
  String get unityRewardedId => Platform.isIOS ? _unityIosRewardedId : _unityAndroidRewardedId;
  
  String get pubscaleAppId => _pubscaleAppId;
  String get pubscaleApiKey => _pubscaleApiKey;

  String getBannerNetworkForScreen(String screen) {
    String specific = 'global';
    switch (screen) {
      case 'home':
        specific = _homeBannerNetwork;
        break;
      case 'details':
        specific = _detailsBannerNetwork;
        break;
      case 'category':
        specific = _categoryBannerNetwork;
        break;
      default:
        specific = 'global';
    }
    
    if (specific == 'none') return 'none';
    if (specific != 'global') return specific;
    
    // Fallback to global
    return _bannerAdNetwork;
  }

  String getNativeNetworkForScreen(String screen) {
    String specific = 'global';
    switch (screen) {
      case 'home':
        specific = _homeNativeNetwork;
        break;
      case 'details':
        specific = _detailsNativeNetwork;
        break;
      case 'category':
        specific = _categoryNativeNetwork;
        break;
      case 'shorts':
        specific = _shortsNativeNetwork;
        break;
      default:
        specific = 'global';
    }

    if (specific == 'none') return 'none';
    if (specific != 'global') return specific;

    return _nativeAdNetwork;
  }

  List<String> _adPriorities = ['admob'];
  List<String> get adPriorities => _adPriorities;

  AdProvider() {
    fetchAdSettings();
  }

  Future<void> fetchAdSettings() async {
    _isLoading = true;
    notifyListeners();

    try {
      final settings = await _apiService.getAdSettings();
      
      if (settings.isNotEmpty) {
        _adsEnabled = settings['ads_enabled'].toString() == '1';
        _bannerEnabled = settings['banner_ads_enabled'].toString() == '1';
        _interstitialEnabled = settings['interstitial_ads_enabled'].toString() == '1';
        _nativeEnabled = settings['native_ads_enabled'].toString() == '1';
        _adProvider = settings['ad_provider'] ?? 'admob_only';
        
        _nativeAdNetwork = settings['native_ad_network'] ?? 'admob';
        _bannerAdNetwork = settings['banner_ad_network'] ?? 'admob';
        _interstitialAdNetwork = settings['interstitial_ad_network'] ?? 'admob';

        // Page Specific Settings
        _homeBannerNetwork = settings['home_banner_network'] ?? 'global';
        _homeNativeNetwork = settings['home_native_network'] ?? 'global';
        _detailsBannerNetwork = settings['details_banner_network'] ?? 'global';
        _detailsNativeNetwork = settings['details_native_network'] ?? 'global';
        _categoryBannerNetwork = settings['category_banner_network'] ?? 'global';
        _categoryNativeNetwork = settings['category_native_network'] ?? 'global';
        _shortsNativeNetwork = settings['shorts_native_network'] ?? 'global';
        _shortsAdInterval = int.tryParse(settings['shorts_ad_interval'].toString()) ?? 5;
        _homeAdInterval = int.tryParse(settings['home_ad_interval'].toString()) ?? 8;

        _admobAndroidAppId = settings['admob_android_app_id'] ?? '';
        _admobAndroidBannerId = settings['admob_android_banner_id'] ?? '';
        _admobAndroidInterstitialId = settings['admob_android_interstitial_id'] ?? '';
        _admobAndroidRewardedId = settings['admob_android_rewarded_id'] ?? '';
        _admobAndroidNativeId = settings['admob_android_native_id'] ?? '';
        
        _admobIosAppId = settings['admob_ios_app_id'] ?? '';
        _admobIosBannerId = settings['admob_ios_banner_id'] ?? '';
        _admobIosInterstitialId = settings['admob_ios_interstitial_id'] ?? '';
        _admobIosRewardedId = settings['admob_ios_rewarded_id'] ?? '';
        _admobIosNativeId = settings['admob_ios_native_id'] ?? '';

        // Facebook
        _facebookAndroidBannerId = settings['facebook_android_banner_id'] ?? '';
        _facebookAndroidInterstitialId = settings['facebook_android_interstitial_id'] ?? '';
        _facebookAndroidNativeId = settings['facebook_android_native_id'] ?? '';
        _facebookAndroidRewardedId = settings['facebook_android_rewarded_id'] ?? '';
        
        _facebookIosBannerId = settings['facebook_ios_banner_id'] ?? '';
        _facebookIosInterstitialId = settings['facebook_ios_interstitial_id'] ?? '';
        _facebookIosNativeId = settings['facebook_ios_native_id'] ?? '';
        _facebookIosRewardedId = settings['facebook_ios_rewarded_id'] ?? '';

        // Unity
        _unityAndroidGameId = settings['unity_android_game_id'] ?? '';
        _unityAndroidBannerId = settings['unity_android_banner_id'] ?? '';
        _unityAndroidInterstitialId = settings['unity_android_interstitial_id'] ?? '';
        _unityAndroidRewardedId = settings['unity_android_rewarded_id'] ?? '';
        
        _unityIosGameId = settings['unity_ios_game_id'] ?? '';
        _unityIosBannerId = settings['unity_ios_banner_id'] ?? '';
        _unityIosInterstitialId = settings['unity_ios_interstitial_id'] ?? '';
        _unityIosRewardedId = settings['unity_ios_rewarded_id'] ?? '';
        
        _pubscaleAppId = settings['pubscale_app_id'] ?? '';
        _pubscaleApiKey = settings['pubscale_api_key'] ?? '';

        // Parse Ad Priorities
        List<String> priorities = [];
        if (settings['ad_priority_1'] != null && settings['ad_priority_1'].toString().isNotEmpty) {
          priorities.add(settings['ad_priority_1'].toString().toLowerCase());
        }
        if (settings['ad_priority_2'] != null && settings['ad_priority_2'].toString().isNotEmpty) {
          priorities.add(settings['ad_priority_2'].toString().toLowerCase());
        }
        if (settings['ad_priority_3'] != null && settings['ad_priority_3'].toString().isNotEmpty) {
          priorities.add(settings['ad_priority_3'].toString().toLowerCase());
        }
        
        // Also check for fallback keys just in case
        if (priorities.isEmpty) {
             if (settings['fallback_priority_1'] != null) priorities.add(settings['fallback_priority_1'].toString().toLowerCase());
             if (settings['fallback_priority_2'] != null) priorities.add(settings['fallback_priority_2'].toString().toLowerCase());
             if (settings['fallback_priority_3'] != null) priorities.add(settings['fallback_priority_3'].toString().toLowerCase());
        }

        if (priorities.isNotEmpty) {
          _adPriorities = priorities;
        }
      }
    } catch (e) {
      debugPrint('Error loading ad settings: $e');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }
}
