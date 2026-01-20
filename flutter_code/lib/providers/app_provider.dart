import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/wallpaper_model.dart';
import '../models/category_model.dart';
import '../models/game_model.dart';
import '../models/banner_model.dart';
import '../services/api_service.dart';

class AppProvider with ChangeNotifier {
  final ApiService _apiService = ApiService();

  int _coins = 0;
  int get coins => _coins;

  List<Wallpaper> _wallpapers = [];
  List<Category> _categories = [];
  List<Game> _games = [];
  List<BannerModel> _banners = [];
  bool _isLoading = false;

  List<Wallpaper> get wallpapers => _wallpapers;
  List<Category> get categories => _categories;
  List<Game> get games => _games;
  List<BannerModel> get banners => _banners;
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
      fetchGames(),
      fetchWallpapers(),
    ]);

    _isLoading = false;
    notifyListeners();
  }

  Future<void> fetchBanners() async {
    _banners = await _apiService.getBanners();
    notifyListeners();
  }

  Future<void> fetchCategories() async {
    _categories = await _apiService.getCategories();
    notifyListeners();
  }

  Future<void> fetchGames() async {
    _games = await _apiService.getGames();
    notifyListeners();
  }

  Future<void> fetchWallpapers() async {
    _wallpapers = await _apiService.getWallpapers();
    notifyListeners();
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
