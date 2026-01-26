import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_mobile_ads/google_mobile_ads.dart';
import 'package:facebook_audience_network/facebook_audience_network.dart';
import '../../providers/ad_provider.dart';

class ShortsNativeAd extends StatefulWidget {
  final VoidCallback? onAdFailed;
  const ShortsNativeAd({super.key, this.onAdFailed});

  @override
  State<ShortsNativeAd> createState() => _ShortsNativeAdState();
}

class _ShortsNativeAdState extends State<ShortsNativeAd> {
  NativeAd? _admobNative;
  bool _isNativeAdLoaded = false;
  String _currentNetwork = '';
  
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _loadAd();
    });
  }

  @override
  void dispose() {
    _admobNative?.dispose();
    super.dispose();
  }

  void _loadAd() {
    final adProvider = Provider.of<AdProvider>(context, listen: false);
    if (!adProvider.adsEnabled || !adProvider.nativeEnabled) {
      if (mounted) setState(() => _currentNetwork = 'none');
      return;
    }

    String network = adProvider.getNativeNetworkForScreen('shorts').toLowerCase();
    
    if (network == 'none') {
      if (mounted) {
        setState(() => _currentNetwork = 'none');
        widget.onAdFailed?.call();
      }
      return;
    }

    _loadNetworkAd(network);
  }

  void _loadNetworkAd(String network) {
    final adProvider = Provider.of<AdProvider>(context, listen: false);

    if (network == 'admob') {
      if (adProvider.admobNativeId.isNotEmpty) {
        _loadAdMobNative();
      } else {
        if (mounted) {
          setState(() => _currentNetwork = 'none');
          widget.onAdFailed?.call();
        }
      }
    } else if (network == 'facebook') {
      if (adProvider.facebookNativeId.isNotEmpty) {
        if (mounted) setState(() => _currentNetwork = 'facebook');
      } else {
        if (mounted) {
          setState(() => _currentNetwork = 'none');
          widget.onAdFailed?.call();
        }
      }
    } else {
      if (mounted) {
        setState(() => _currentNetwork = 'none');
        widget.onAdFailed?.call();
      }
    }
  }

  Future<void> _loadAdMobNative() async {
    final adProvider = Provider.of<AdProvider>(context, listen: false);
    
    _admobNative = NativeAd(
      adUnitId: adProvider.admobNativeId,
      factoryId: 'shortsNative', // Using the dedicated full-screen factory
      request: const AdRequest(),
      listener: NativeAdListener(
        onAdLoaded: (ad) {
           debugPrint("✅ AdMob Native (Shorts) Loaded");
           if (mounted) {
             setState(() {
               _isNativeAdLoaded = true;
               _currentNetwork = 'admob';
             });
           }
        },
        onAdFailedToLoad: (ad, error) {
          debugPrint("❌ AdMob Native (Shorts) Failed: $error");
          ad.dispose();
          if (mounted) {
            setState(() => _currentNetwork = 'none');
            widget.onAdFailed?.call();
          }
        }
      ),
    );
    
    _admobNative?.load();
  }

  @override
  Widget build(BuildContext context) {
    if (_currentNetwork == 'none') {
      // If ad fails to load, we should probably show a placeholder or nothing. 
      // In a PageView, returning SizedBox.shrink() might leave a blank page.
      // Better to show a "Loading..." or handle it in parent to skip this page.
      // For now, let's show a dark background.
      return Container(color: Colors.black);
    }

    if (_currentNetwork == 'admob' && _isNativeAdLoaded && _admobNative != null) {
      return Container(
        color: Colors.black,
        child: SizedBox.expand(
          child: AdWidget(ad: _admobNative!),
        ),
      );
    }

    if (_currentNetwork == 'facebook') {
      final adProvider = Provider.of<AdProvider>(context, listen: false);
      return Container(
        color: Colors.black,
        child: Center(
          child: FacebookNativeAd(
            placementId: adProvider.facebookNativeId,
            adType: NativeAdType.NATIVE_AD,
            width: double.infinity,
            height: double.infinity,
            backgroundColor: Colors.black,
            titleColor: Colors.white,
            descriptionColor: Colors.white70,
            buttonColor: Colors.deepPurple,
            buttonTitleColor: Colors.white,
            buttonBorderColor: Colors.white,
            keepAlive: true,
            listener: (result, value) {
              if (result == NativeAdResult.ERROR) {
                 if (mounted) setState(() => _currentNetwork = 'none');
              }
            },
          ),
        ),
      );
    }

    return Container(
      color: Colors.black,
      child: const Center(
        child: CircularProgressIndicator(color: Colors.white),
      ),
    );
  }
}
