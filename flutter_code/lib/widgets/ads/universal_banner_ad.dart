import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_mobile_ads/google_mobile_ads.dart';
import 'package:facebook_audience_network/facebook_audience_network.dart' as fb;
import 'package:unity_ads_plugin/unity_ads_plugin.dart' as unity;
import '../../providers/ad_provider.dart';

class UniversalBannerAd extends StatefulWidget {
  final String screen;
  const UniversalBannerAd({super.key, this.screen = 'global'});

  @override
  State<UniversalBannerAd> createState() => _UniversalBannerAdState();
}

class _UniversalBannerAdState extends State<UniversalBannerAd> {
  BannerAd? _admobBanner;
  bool _isAdmobLoaded = false;
  String _currentNetwork = '';
  List<String> _priorities = [];
  int _currentIndex = 0;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _loadAd();
    });
  }

  @override
  void dispose() {
    _admobBanner?.dispose();
    super.dispose();
  }

  void _loadAd() {
    final adProvider = Provider.of<AdProvider>(context, listen: false);
    if (!adProvider.adsEnabled || !adProvider.bannerEnabled) return;

    // Use screen-specific network setting
    String selectedNetwork = adProvider.getBannerNetworkForScreen(widget.screen).toLowerCase();
    
    if (selectedNetwork != 'none' && selectedNetwork != 'auto' && selectedNetwork.isNotEmpty) {
       _priorities = [selectedNetwork];
    } else if (selectedNetwork == 'none') {
       if (mounted) setState(() => _currentNetwork = 'none');
       return;
    } else {
       _priorities = adProvider.adPriorities.isNotEmpty 
          ? adProvider.adPriorities 
          : ['admob', 'facebook', 'unity'];
    }

    _currentIndex = 0;

    _tryLoadNext(_currentIndex);
  }

  Future<void> _tryLoadNext(int index) async {
    if (index >= _priorities.length) {
      if (mounted) setState(() => _currentNetwork = 'none');
      return;
    }

    _currentIndex = index;
    final network = _priorities[index].toLowerCase();
    final adProvider = Provider.of<AdProvider>(context, listen: false);

    if (network == 'admob') {
      if (adProvider.admobBannerId.isNotEmpty) {
        await _loadAdMob(onFailed: () => _tryLoadNext(index + 1));
      } else {
        _tryLoadNext(index + 1);
      }
    } else if (network == 'facebook') {
      if (adProvider.facebookBannerId.isNotEmpty) {
        if (mounted) {
           setState(() {
             _currentNetwork = 'facebook';
           });
        }
      } else {
        _tryLoadNext(index + 1);
      }
    } else if (network == 'unity') {
      if (adProvider.unityBannerId.isNotEmpty) {
         if (mounted) {
           setState(() {
             _currentNetwork = 'unity';
           });
        }
      } else {
        _tryLoadNext(index + 1);
      }
    } else {
      _tryLoadNext(index + 1);
    }
  }

  Future<void> _loadAdMob({required VoidCallback onFailed}) async {
    final adProvider = Provider.of<AdProvider>(context, listen: false);
    
    _admobBanner = BannerAd(
      adUnitId: adProvider.admobBannerId,
      size: AdSize.banner,
      request: const AdRequest(),
      listener: BannerAdListener(
        onAdLoaded: (ad) {
          print("✅ AdMob Banner Loaded");
          if (mounted) {
            setState(() {
              _isAdmobLoaded = true;
              _currentNetwork = 'admob';
            });
          }
        },
        onAdFailedToLoad: (ad, error) {
          print("❌ AdMob Banner Failed: $error");
          ad.dispose();
          onFailed();
        },
      ),
    );

    await _admobBanner!.load();
  }

  @override
  Widget build(BuildContext context) {
    final adProvider = Provider.of<AdProvider>(context);
    
    if (!adProvider.adsEnabled || !adProvider.bannerEnabled) {
      return const SizedBox.shrink();
    }

    if (_currentNetwork == 'admob' && _isAdmobLoaded && _admobBanner != null) {
      return Container(
        alignment: Alignment.center,
        width: _admobBanner!.size.width.toDouble(),
        height: _admobBanner!.size.height.toDouble(),
        child: AdWidget(ad: _admobBanner!),
      );
    }

    if (_currentNetwork == 'facebook') {
      return Container(
        alignment: Alignment.center,
        child: fb.FacebookBannerAd(
          placementId: adProvider.facebookBannerId,
          bannerSize: fb.BannerSize.STANDARD,
          listener: (result, value) {
            if (result == fb.BannerAdResult.ERROR) {
              print("❌ Facebook Banner Error: $value");
              _tryLoadNext(_currentIndex + 1);
            }
          },
        ),
      );
    }

    if (_currentNetwork == 'unity') {
      return Container(
        alignment: Alignment.center,
        child: unity.UnityBannerAd(
          placementId: adProvider.unityBannerId,
          onLoad: (placementId) => print('✅ Unity Banner Loaded: $placementId'),
          onFailed: (placementId, error, message) {
            print('❌ Unity Banner Failed: $placementId, $error, $message');
            _tryLoadNext(_currentIndex + 1);
          },
        ),
      );
    }

    return const SizedBox.shrink();
  }
}
