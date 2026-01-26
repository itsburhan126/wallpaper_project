import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_mobile_ads/google_mobile_ads.dart';
import 'package:facebook_audience_network/facebook_audience_network.dart';
import '../../providers/ad_provider.dart';

class UniversalNativeAd extends StatefulWidget {
  final bool isGrid;
  final String screen;
  const UniversalNativeAd({super.key, this.isGrid = true, this.screen = 'global'});

  @override
  State<UniversalNativeAd> createState() => _UniversalNativeAdState();
}

class _UniversalNativeAdState extends State<UniversalNativeAd> {
  BannerAd? _admobBanner; // Using MREC as Native substitute
  NativeAd? _admobNative; // Using Real Native Advanced
  bool _isAdmobLoaded = false;
  bool _isNativeAdLoaded = false;
  String _currentNetwork = '';
  
  // For loading retries
  // final int _retryAttempt = 0;

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
    _admobNative?.dispose();
    super.dispose();
  }

  void _loadAd() {
    final adProvider = Provider.of<AdProvider>(context, listen: false);
    if (!adProvider.adsEnabled || !adProvider.nativeEnabled) {
      if (mounted) setState(() => _currentNetwork = 'none');
      return;
    }

    // Get the selected network from admin
    String network = adProvider.getNativeNetworkForScreen(widget.screen).toLowerCase();
    
    // If set to 'none', don't load
    if (network == 'none') {
      if (mounted) setState(() => _currentNetwork = 'none');
      return;
    }

    _loadNetworkAd(network);
  }

  void _loadNetworkAd(String network) {
    final adProvider = Provider.of<AdProvider>(context, listen: false);

    if (network == 'admob') {
      if (adProvider.admobNativeId.isNotEmpty) {
        _loadAdMobMREC();
      } else {
        // Fallback or none? The user wants strict control, so maybe none.
        if (mounted) setState(() => _currentNetwork = 'none');
      }
    } else if (network == 'facebook') {
      if (adProvider.facebookNativeId.isNotEmpty) {
        if (mounted) setState(() => _currentNetwork = 'facebook');
      } else {
        if (mounted) setState(() => _currentNetwork = 'none');
      }
    } else {
      if (mounted) setState(() => _currentNetwork = 'none');
    }
  }

  Future<void> _loadAdMobMREC() async {
    final adProvider = Provider.of<AdProvider>(context, listen: false);
    
    // We are using a BannerAd widget to display an MREC ad.
    // Ensure the adUnitId provided in admin is actually an MREC ad unit ID, or compatible.
    // If the user provided a Native Advanced ID, this will fail with "Ad unit doesn't match format".
    // MREC (300x250) is a standard banner size.
    
    _admobBanner = BannerAd(
      adUnitId: adProvider.admobNativeId, 
      size: AdSize.mediumRectangle,
      request: const AdRequest(),
      listener: BannerAdListener(
        onAdLoaded: (ad) {
          debugPrint("✅ AdMob Native (MREC) Loaded");
          if (mounted) {
            setState(() {
              _isAdmobLoaded = true;
              _currentNetwork = 'admob';
            });
          }
        },
        onAdFailedToLoad: (ad, error) {
          debugPrint("❌ AdMob Native (MREC) Failed: $error");
          ad.dispose();
          // If MREC fails (likely because ID is for Native Advanced), try Native Ad?
          if (error.code == 3 || error.message.contains("Ad unit doesn't match format")) {
             debugPrint("⚠️ MREC failed format check, trying Native Advanced...");
             _loadAdMobNativeAdvanced();
          } else {
             if (mounted) setState(() => _currentNetwork = 'none');
          }
        },
      ),
    );

    _admobBanner?.load();
  }

  Future<void> _loadAdMobNativeAdvanced() async {
    final adProvider = Provider.of<AdProvider>(context, listen: false);
    
    _admobNative = NativeAd(
      adUnitId: adProvider.admobNativeId,
      factoryId: 'listTile', // Ensure this matches your Android/iOS factory ID
      request: const AdRequest(),
      listener: NativeAdListener(
        onAdLoaded: (ad) {
           debugPrint("✅ AdMob Native Advanced Loaded");
           if (mounted) {
             setState(() {
               _isNativeAdLoaded = true;
               _currentNetwork = 'admob_native';
             });
           }
        },
        onAdFailedToLoad: (ad, error) {
          debugPrint("❌ AdMob Native Advanced Failed: $error");
          ad.dispose();
          if (mounted) setState(() => _currentNetwork = 'none');
        }
      ),
    );
    
    _admobNative?.load();
  }

  @override
  Widget build(BuildContext context) {
    if (_currentNetwork == 'none') return const SizedBox.shrink();

    if (_currentNetwork == 'admob' && _isAdmobLoaded && _admobBanner != null) {
      return Container(
        margin: const EdgeInsets.all(4),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.1),
              blurRadius: 4,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: ClipRRect(
          borderRadius: BorderRadius.circular(12),
          child: Center(
            child: SizedBox(
              width: _admobBanner!.size.width.toDouble(),
              height: _admobBanner!.size.height.toDouble(),
              child: AdWidget(ad: _admobBanner!),
            ),
          ),
        ),
      );
    }

    if (_currentNetwork == 'admob_native' && _isNativeAdLoaded && _admobNative != null) {
      return Container(
        margin: const EdgeInsets.symmetric(vertical: 8, horizontal: 4),
        height: 320, // Reduced height for more compact look
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          boxShadow: [
             BoxShadow(
              color: Colors.black.withValues(alpha: 0.1),
              blurRadius: 4,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: ClipRRect(
          borderRadius: BorderRadius.circular(12),
          child: AdWidget(ad: _admobNative!),
        ),
      );
    }

    if (_currentNetwork == 'facebook') {
       final adProvider = Provider.of<AdProvider>(context, listen: false);
       return Container(
         margin: const EdgeInsets.all(4),
         decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.1),
              blurRadius: 4,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: ClipRRect(
          borderRadius: BorderRadius.circular(12),
          child: FacebookNativeAd(
            placementId: adProvider.facebookNativeId,
            adType: NativeAdType.NATIVE_AD,
            width: double.infinity,
            height: double.infinity,
            backgroundColor: Colors.white,
            titleColor: Colors.black,
            descriptionColor: Colors.grey,
            buttonColor: Colors.blue,
            buttonTitleColor: Colors.white,
            buttonBorderColor: Colors.blue,
            listener: (result, value) {
              if (result == NativeAdResult.ERROR) {
                debugPrint("❌ Facebook Native Ad Error: $value");
                if (mounted) setState(() => _currentNetwork = 'none');
              }
            },
            keepAlive: true,
          ),
        ),
       );
    }

    return const SizedBox.shrink();
  }
}
