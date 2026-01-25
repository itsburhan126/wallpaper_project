import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import 'providers/app_provider.dart';
import 'providers/shorts_provider.dart';
import 'providers/ad_provider.dart';
import 'screens/splash_screen.dart';
import 'package:wallpaper_app/utils/constants.dart';
import 'package:wallpaper_app/utils/app_theme.dart';
import 'package:google_mobile_ads/google_mobile_ads.dart';
import 'providers/language_provider.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  // Initialize AdMob SDK
  await MobileAds.instance.initialize();

  // Set Test Device IDs
  MobileAds.instance.updateRequestConfiguration(
    RequestConfiguration(testDeviceIds: ["152DBBC170F24DADADA7525FC86F3175"]),
  );

  SystemChrome.setSystemUIOverlayStyle(const SystemUiOverlayStyle(
    statusBarColor: Colors.transparent,
    statusBarIconBrightness: Brightness.light,
    systemNavigationBarColor: Colors.transparent,
    systemNavigationBarDividerColor: Colors.transparent,
    systemNavigationBarIconBrightness: Brightness.light,
    systemNavigationBarContrastEnforced: false,
  ));
  SystemChrome.setEnabledSystemUIMode(SystemUiMode.edgeToEdge);
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AppProvider()),
        ChangeNotifierProvider(create: (_) => ShortsProvider()),
        ChangeNotifierProvider(create: (_) => AdProvider()),
        ChangeNotifierProvider(create: (_) => LanguageProvider())
      ],
      child: Consumer<LanguageProvider>(
        builder: (context, languageProvider, child) {
          return MaterialApp(
            title: 'Prime Walls',
            debugShowCheckedModeBanner: false,
            locale: languageProvider.currentLocale,
            theme: ThemeData(
              useMaterial3: true,
              scaffoldBackgroundColor: AppTheme.darkBackgroundColor,
              primaryColor: AppColors.primary,
              colorScheme: ColorScheme.dark(
                primary: AppColors.primary,
                secondary: AppColors.secondary,
                background: AppTheme.darkBackgroundColor,
                surface: AppColors.cardColor,
              ),
              appBarTheme: const AppBarTheme(
                backgroundColor: AppTheme.darkBackgroundColor,
                elevation: 0,
                centerTitle: true,
              ),
              pageTransitionsTheme: const PageTransitionsTheme(
                builders: {
                  TargetPlatform.android: ZoomPageTransitionsBuilder(),
                  TargetPlatform.iOS: CupertinoPageTransitionsBuilder(),
                  TargetPlatform.fuchsia: ZoomPageTransitionsBuilder(),
                },
              ),
            ),
            home: const SplashScreen(),
          );
        },
      ),
    );
  }
}
