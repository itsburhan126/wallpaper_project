import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/wallpaper_model.dart';
import '../models/category_model.dart';
import '../models/banner_model.dart';
import '../models/game_model.dart';
import '../services/api_service.dart';

class AppProvider with ChangeNotifier {
  final ApiService _apiService = ApiService();

  int _coins = 0;
  int get coins => _coins;

  String _userName = "Guest User";
  String get userName => _userName;

  int _userLevel = 1;
  int get userLevel => _userLevel;

  int _wallpaperViewTime = 10;
  int get wallpaperViewTime => _wallpaperViewTime;

  String _currencySymbol = '\$';
  int _coinRate = 1000;

  // Watch Ads Settings
  int _watchAdsLimit = 10;
  int _watchAdsReward = 50;
  List<String> _watchAdsPriorities = ['admob', 'admob', 'admob'];
  int _watchedAdsCount = 0;

  // Lucky Wheel Settings
  int _luckyWheelLimit = 10;
  List<int> _luckyWheelRewards = [10, 20, 30, 40, 50, 60, 70, 80];
  List<String> _luckyWheelPriorities = ['admob', 'admob', 'admob'];
  int _luckyWheelSpinsCount = 0;

  String get currencySymbol => _currencySymbol;
  int get coinRate => _coinRate;
  
  int get watchAdsLimit => _watchAdsLimit;
  int get watchAdsReward => _watchAdsReward;
  List<String> get watchAdsPriorities => _watchAdsPriorities;
  int get watchedAdsCount => _watchedAdsCount;

  int get luckyWheelLimit => _luckyWheelLimit;
  List<int> get luckyWheelRewards => _luckyWheelRewards;
  List<String> get luckyWheelPriorities => _luckyWheelPriorities;
  int get luckyWheelSpinsCount => _luckyWheelSpinsCount;

  List<Wallpaper> _wallpapers = [];
  List<Category> _categories = [];
  List<BannerModel> _banners = [];
  List<GameModel> _games = [];
  bool _isLoading = false;

  // Daily Rewards State
  List<dynamic> _dailyRewards = [];
  bool _canClaimDailyReward = false;
  int _currentDay = 1;
  bool _isDailyRewardLoading = false;
  List<String> _dailyRewardAdPriorities = [];

  List<dynamic> get dailyRewards => _dailyRewards;
  bool get canClaimDailyReward => _canClaimDailyReward;
  int get currentDay => _currentDay;
  bool get isDailyRewardLoading => _isDailyRewardLoading;
  List<String> get dailyRewardAdPriorities => _dailyRewardAdPriorities;

  List<Wallpaper> get wallpapers => _wallpapers;
  List<Category> get categories => _categories;
  List<BannerModel> get banners => _banners;
  List<GameModel> get games => _games;
  bool get isLoading => _isLoading;

  AppProvider() {
    _loadInitialData();
  }

  bool _isUserLoading = false;
  bool get isUserLoading => _isUserLoading;

  Future<void> _loadInitialData() async {
    _isLoading = true;
    notifyListeners();

    // Load Local Coins
    final prefs = await SharedPreferences.getInstance();
    _coins = prefs.getInt('coins') ?? 0;
    
    // Check if user is logged in
    final token = prefs.getString('auth_token');

    // Load Watch Ads Count (Reset if new day)
    String today = DateTime.now().toIso8601String().split('T')[0];
    String? lastAdDate = prefs.getString('last_ad_date');
    if (lastAdDate != today) {
      _watchedAdsCount = 0;
      await prefs.setString('last_ad_date', today);
      await prefs.setInt('watched_ads_count', 0);
    } else {
      _watchedAdsCount = prefs.getInt('watched_ads_count') ?? 0;
    }

    // Load Lucky Wheel Count
    String? lastSpinDate = prefs.getString('last_spin_date');
    if (lastSpinDate != today) {
      _luckyWheelSpinsCount = 0;
      await prefs.setString('last_spin_date', today);
      await prefs.setInt('lucky_wheel_spins_count', 0);
    } else {
      _luckyWheelSpinsCount = prefs.getInt('lucky_wheel_spins_count') ?? 0;
    }

    // Fetch API Data
    await Future.wait([
      fetchBanners(),
      fetchCategories(),
      fetchWallpapers(),
      fetchGeneralSettings(),
      fetchDailyRewards(),
      if (token != null) fetchUserBalance(), // Fetch user data if logged in
    ]);

    _isLoading = false;
    notifyListeners();
  }

  Future<void> fetchBanners() async {
    _banners = await _apiService.getBanners();
    notifyListeners();
  }

  Future<void> fetchGames() async {
    _games = await _apiService.getGames();
    notifyListeners();
  }

  Future<void> fetchCategories() async {
    _categories = await _apiService.getCategories();
    notifyListeners();
  }

  Future<void> fetchWallpapers() async {
    _wallpapers = await _apiService.getWallpapers();
    notifyListeners();
  }

  Future<void> fetchGeneralSettings() async {
    final settings = await _apiService.getGeneralSettings();
    if (settings.containsKey('wallpaper_view_time')) {
      _wallpaperViewTime = int.tryParse(settings['wallpaper_view_time'].toString()) ?? 10;
    }
    if (settings.containsKey('currency_symbol')) {
      _currencySymbol = settings['currency_symbol'].toString();
    }
    if (settings.containsKey('coin_rate')) {
      _coinRate = int.tryParse(settings['coin_rate'].toString()) ?? 1000;
    }

    if (settings.containsKey('watch_ads_limit')) {
      _watchAdsLimit = int.tryParse(settings['watch_ads_limit'].toString()) ?? 10;
    }
    if (settings.containsKey('watch_ads_reward')) {
      _watchAdsReward = int.tryParse(settings['watch_ads_reward'].toString()) ?? 50;
    }
    
    // Parse Watch Ads priorities
    List<String> priorities = [];
    if (settings.containsKey('watch_ads_priority_1')) priorities.add(settings['watch_ads_priority_1'].toString());
    if (settings.containsKey('watch_ads_priority_2')) priorities.add(settings['watch_ads_priority_2'].toString());
    if (settings.containsKey('watch_ads_priority_3')) priorities.add(settings['watch_ads_priority_3'].toString());
    
    if (priorities.isNotEmpty) {
        _watchAdsPriorities = priorities;
    }

    // Lucky Wheel Parsing
    if (settings.containsKey('lucky_wheel_limit')) {
      _luckyWheelLimit = int.tryParse(settings['lucky_wheel_limit'].toString()) ?? 10;
    }
    
    List<int> rewards = [];
    for (int i = 1; i <= 8; i++) {
        if (settings.containsKey('lucky_wheel_reward_$i')) {
            rewards.add(int.tryParse(settings['lucky_wheel_reward_$i'].toString()) ?? (i * 10));
        } else {
            rewards.add(i * 10);
        }
    }
    if (rewards.isNotEmpty) _luckyWheelRewards = rewards;

    List<String> wheelPriorities = [];
    if (settings.containsKey('lucky_wheel_priority_1')) wheelPriorities.add(settings['lucky_wheel_priority_1'].toString());
    if (settings.containsKey('lucky_wheel_priority_2')) wheelPriorities.add(settings['lucky_wheel_priority_2'].toString());
    if (settings.containsKey('lucky_wheel_priority_3')) wheelPriorities.add(settings['lucky_wheel_priority_3'].toString());
    if (wheelPriorities.isNotEmpty) _luckyWheelPriorities = wheelPriorities;

    notifyListeners();
  }

  String _userEmail = "";
  String get userEmail => _userEmail;

  String _userId = "";
  String get userId => _userId;

  Future<void> fetchUserBalance() async {
    _isUserLoading = true;
    notifyListeners();
    
    try {
      final result = await _apiService.getUserDetails();
      if (result['success'] == true) {
        final data = result['data'];
        if (data != null) {
          // Coins/Balance
          if (data['coins'] != null) {
            _coins = int.tryParse(data['coins'].toString()) ?? _coins;
          } else if (data['balance'] != null) {
            _coins = int.tryParse(data['balance'].toString()) ?? _coins;
          } else if (data['wallet'] != null) {
            _coins = int.tryParse(data['wallet'].toString()) ?? _coins;
          }

          // Name/Username
          if (data['name'] != null) {
            _userName = data['name'].toString();
          } else if (data['username'] != null) {
            _userName = data['username'].toString();
          }

          // Level
          if (data['level'] != null) {
             _userLevel = int.tryParse(data['level'].toString()) ?? 1;
          }

          // Email
          if (data['email'] != null) {
            _userEmail = data['email'].toString();
          }

          // ID
          if (data['id'] != null) {
            _userId = data['id'].toString();
          }
          
          // Save updated coins locally
          final prefs = await SharedPreferences.getInstance();
          await prefs.setInt('coins', _coins);
        }
      }
    } catch (e) {
      print("Error fetching user balance: $e");
    } finally {
      _isUserLoading = false;
      notifyListeners();
    }
  }

  Future<void> fetchDailyRewards() async {
    _isDailyRewardLoading = true;
    notifyListeners();

    final data = await _apiService.fetchDailyRewards();
    if (data.isNotEmpty) {
      _dailyRewards = data['rewards'] ?? [];
      _canClaimDailyReward = data['can_claim'] ?? false;
      _currentDay = int.tryParse(data['next_day'].toString()) ?? 1;
      
      if (data['ad_config'] != null) {
        _dailyRewardAdPriorities = List<String>.from(data['ad_config']);
      }
    }

    _isDailyRewardLoading = false;
    notifyListeners();
  }

  Future<bool> claimDailyReward() async {
    final success = await _apiService.claimDailyReward();
    if (success) {
      await fetchUserBalance(); // Refresh coins
      await fetchDailyRewards(); // Refresh status
    }
    return success;
  }

  Future<void> addCoins(int amount) async {
    _coins += amount;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setInt('coins', _coins);
    notifyListeners();

    // Sync with Server
    await _apiService.updateBalance(amount);
  }

  Future<void> incrementWatchedAdsCount() async {
    _watchedAdsCount++;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setInt('watched_ads_count', _watchedAdsCount);
    notifyListeners();
  }

  Future<void> incrementLuckyWheelSpinsCount() async {
    _luckyWheelSpinsCount++;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setInt('lucky_wheel_spins_count', _luckyWheelSpinsCount);
    notifyListeners();
  }

  // Admin Actions removed from Provider as they are now handled via Admin Panel
}
