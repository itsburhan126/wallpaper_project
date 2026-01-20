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

  int _wallpaperViewTime = 10;
  int get wallpaperViewTime => _wallpaperViewTime;

  String _currencySymbol = '\$';
  int _coinRate = 1000;

  String get currencySymbol => _currencySymbol;
  int get coinRate => _coinRate;

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

  Future<void> _loadInitialData() async {
    _isLoading = true;
    notifyListeners();

    // Load Local Coins
    final prefs = await SharedPreferences.getInstance();
    _coins = prefs.getInt('coins') ?? 0;

    // Fetch API Data
    await Future.wait([
      fetchBanners(),
      fetchCategories(),
      fetchWallpapers(),
      fetchGeneralSettings(),
      fetchDailyRewards(),
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
    notifyListeners();
  }

  Future<void> fetchUserBalance() async {
    final result = await _apiService.getUserDetails();
    if (result['success'] == true) {
      final data = result['data'];
      if (data != null && data['coins'] != null) {
        _coins = int.tryParse(data['coins'].toString()) ?? _coins;
        notifyListeners();
      }
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

  // Admin Actions removed from Provider as they are now handled via Admin Panel
}
