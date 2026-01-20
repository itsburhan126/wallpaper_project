import 'package:flutter/foundation.dart';
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

  // AdMob IDs
  String? _admobAppId;
  String? _admobBannerId;
  String? _admobInterstitialId;
  String? _admobRewardedId;
  String? _admobNativeId;

  // Getters
  bool get adsEnabled => _adsEnabled;
  bool get bannerEnabled => _adsEnabled && _bannerEnabled;
  bool get interstitialEnabled => _adsEnabled && _interstitialEnabled;
  bool get nativeEnabled => _adsEnabled && _nativeEnabled;
  String get adProvider => _adProvider;

  String? get admobAppId => _admobAppId;
  String? get admobBannerId => _admobBannerId;
  String? get admobInterstitialId => _admobInterstitialId;
  String? get admobRewardedId => _admobRewardedId;
  String? get admobNativeId => _admobNativeId;

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

        _admobAppId = settings['admob_android_app_id'];
        _admobBannerId = settings['admob_android_banner_id'];
        _admobInterstitialId = settings['admob_android_interstitial_id'];
        _admobRewardedId = settings['admob_android_rewarded_id'];
        _admobNativeId = settings['admob_android_native_id'];
      }
    } catch (e) {
      print('Error loading ad settings: $e');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }
}
