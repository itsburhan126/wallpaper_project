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

  String get currencySymbol => _currencySymbol;
  int get coinRate => _coinRate;

  // Game Settings
  int _gameDailyLimit = 10;
  int _gamesPlayedToday = 0;
  
  int get gameDailyLimit => _gameDailyLimit;
  int get gamesPlayedToday => _gamesPlayedToday;
  bool get canPlayGame => _gamesPlayedToday < _gameDailyLimit;

  // Watch Ads Settings
  int _adDailyLimit = 10;
  int _adsWatchedToday = 0;

  int _watchAdsReward = 50;
  List<String> _watchAdsPriorities = ['admob', 'admob', 'admob'];
  
  int get adDailyLimit => _adDailyLimit;
  int get adsWatchedToday => _adsWatchedToday;
  bool get canWatchAd => _adsWatchedToday < _adDailyLimit;

  int get watchAdsReward => _watchAdsReward;
  List<String> get watchAdsPriorities => _watchAdsPriorities;
  
  // Lucky Wheel Settings
  int _luckyWheelLimit = 10;
  List<int> _luckyWheelRewards = [10, 20, 30, 40, 50, 60, 70, 80];
  List<String> _luckyWheelPriorities = ['admob', 'admob', 'admob'];
  int _luckyWheelSpinsCount = 0;

  // Game Settings

  // int get watchAdsLimit => _watchAdsLimit; // Deprecated
  // int get watchAdsReward => _watchAdsReward;
  // List<String> get watchAdsPriorities => _watchAdsPriorities;
  // int get watchedAdsCount => _watchedAdsCount;

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

    // Load Local Data
    final prefs = await SharedPreferences.getInstance();
    _coins = prefs.getInt('coins') ?? 0;
    
    // Load User Data
    _userName = prefs.getString('user_name') ?? "Guest User";
    _userAvatar = prefs.getString('user_avatar') ?? "";
    _userEmail = prefs.getString('user_email') ?? "";
    _userId = prefs.getString('user_id') ?? "";
    _userLevel = prefs.getInt('user_level') ?? 1;

    // Check if user is logged in
    final token = prefs.getString('auth_token');

    // Load Watch Ads Count (Reset if new day)
    String today = DateTime.now().toIso8601String().split('T')[0];
    String? lastAdDate = prefs.getString('last_ad_date');
    if (lastAdDate != today) {
      _adsWatchedToday = 0;
      await prefs.setString('last_ad_date', today);
      await prefs.setInt('ads_watched_today', 0);
    } else {
      _adsWatchedToday = prefs.getInt('ads_watched_today') ?? 0;
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

    // Load Game Play Count
    String? lastGameDate = prefs.getString('last_game_date');
    if (lastGameDate != today) {
      _gamesPlayedToday = 0;
      await prefs.setString('last_game_date', today);
      await prefs.setInt('games_played_today', 0);
    } else {
      _gamesPlayedToday = prefs.getInt('games_played_today') ?? 0;
    }

    // Fetch API Data
    await Future.wait([
      fetchBanners(),
      fetchCategories(),
      fetchWallpapers(),
      fetchGeneralSettings(),
      fetchGameSettings(),
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
    await fetchGameStatus();
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

    if (settings.containsKey('ad_daily_limit')) {
      _adDailyLimit = int.tryParse(settings['ad_daily_limit'].toString()) ?? 10;
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

  Future<void> fetchGameSettings() async {
    // Also fetch status to get latest limit and played count
    await fetchGameStatus();
  }

  Future<void> fetchGameStatus() async {
    final data = await _apiService.fetchGameStatus();
    if (data.isNotEmpty) {
      if (data['daily_limit'] != null) {
        _gameDailyLimit = int.tryParse(data['daily_limit'].toString()) ?? 10;
      }
      if (data['games_played_today'] != null) {
        _gamesPlayedToday = int.tryParse(data['games_played_today'].toString()) ?? 0;
        final prefs = await SharedPreferences.getInstance();
        await prefs.setInt('games_played_today', _gamesPlayedToday);
      }
      
      if (data['ad_daily_limit'] != null) {
        _adDailyLimit = int.tryParse(data['ad_daily_limit'].toString()) ?? 10;
      }
      if (data['ads_watched_today'] != null) {
        _adsWatchedToday = int.tryParse(data['ads_watched_today'].toString()) ?? 0;
        final prefs = await SharedPreferences.getInstance();
        await prefs.setInt('ads_watched_today', _adsWatchedToday);
      }
      notifyListeners();
    }
  }

  String _userEmail = "";
  String get userEmail => _userEmail;

  String _userId = "";
  String get userId => _userId;

  String _userAvatar = "";
  String get userAvatar => _userAvatar;

  Future<void> setUser(Map<String, dynamic> data) async {
    print("------- PROVIDER SET USER DEBUG -------");
    print("Received Data: $data");
    
    final prefs = await SharedPreferences.getInstance();

    if (data['user'] != null) {
      final userData = data['user'];
      
      if (userData['coins'] != null) {
        _coins = int.tryParse(userData['coins'].toString()) ?? _coins;
      }
      
      if (userData['name'] != null) {
        _userName = userData['name'].toString();
        await prefs.setString('user_name', _userName);
      }
      
      if (userData['level'] != null) {
        _userLevel = int.tryParse(userData['level'].toString()) ?? 1;
        await prefs.setInt('user_level', _userLevel);
      }
      
      if (userData['email'] != null) {
        _userEmail = userData['email'].toString();
        await prefs.setString('user_email', _userEmail);
      }
      
      if (userData['id'] != null) {
        _userId = userData['id'].toString();
        await prefs.setString('user_id', _userId);
      }
      
      if (userData['avatar'] != null) {
        _userAvatar = userData['avatar'].toString();
        await prefs.setString('user_avatar', _userAvatar);
      }

      // Sync Game Limit from Server
      if (userData['daily_game_count'] != null) {
        _gamesPlayedToday = int.tryParse(userData['daily_game_count'].toString()) ?? 0;
        await prefs.setInt('games_played_today', _gamesPlayedToday);
      }
      
      // Sync Ad Limit from Server
      if (userData['daily_ad_count'] != null) {
        _adsWatchedToday = int.tryParse(userData['daily_ad_count'].toString()) ?? 0;
        await prefs.setInt('ads_watched_today', _adsWatchedToday);
      }
      
      if (userData['last_game_date'] != null) {
        // Assuming format YYYY-MM-DD
        String serverDate = userData['last_game_date'].toString().split(' ')[0];
        await prefs.setString('last_game_date', serverDate);
      }
      
      // Also update directly if fields exist in root (fallback)
      if (data['coins'] != null) _coins = int.tryParse(data['coins'].toString()) ?? _coins;
    } else {
      // Original logic for flat structure
      if (data['coins'] != null) {
        _coins = int.tryParse(data['coins'].toString()) ?? _coins;
      } else if (data['balance'] != null) {
        _coins = int.tryParse(data['balance'].toString()) ?? _coins;
      } else if (data['wallet'] != null) {
        _coins = int.tryParse(data['wallet'].toString()) ?? _coins;
      }
      
      if (data['name'] != null) {
        _userName = data['name'].toString();
        await prefs.setString('user_name', _userName);
      } else if (data['username'] != null) {
        _userName = data['username'].toString();
        await prefs.setString('user_name', _userName);
      }
      
      if (data['level'] != null) {
        _userLevel = int.tryParse(data['level'].toString()) ?? 1;
        await prefs.setInt('user_level', _userLevel);
      }
      
      if (data['email'] != null) {
        _userEmail = data['email'].toString();
        await prefs.setString('user_email', _userEmail);
      }
      
      if (data['id'] != null) {
        _userId = data['id'].toString();
        await prefs.setString('user_id', _userId);
      }
      
      if (data['avatar'] != null) {
        _userAvatar = data['avatar'].toString();
        await prefs.setString('user_avatar', _userAvatar);
      } else if (data['image'] != null) {
        _userAvatar = data['image'].toString();
        await prefs.setString('user_avatar', _userAvatar);
      }
    }
    
    await prefs.setInt('coins', _coins);
    notifyListeners();
  }

  Future<void> fetchUserBalance() async {
    _isUserLoading = true;
    notifyListeners();
    
    try {
      final result = await _apiService.getUserDetails();
      if (result['success'] == true) {
        final data = result['data'];
        if (data != null) {
          await setUser(data);
          
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

  Future<void> addCoins(int amount, {String source = 'app_activity', String? gameId}) async {
    _coins += amount;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setInt('coins', _coins);

    // Optimistic Update for Ads
    if (source == 'ad_watch') {
      _adsWatchedToday++;
      await prefs.setInt('ads_watched_today', _adsWatchedToday);
    }

    // Optimistic Update for Lucky Wheel
    if (source == 'lucky_wheel') {
      _luckyWheelSpinsCount++;
      await prefs.setInt('lucky_wheel_spins_count', _luckyWheelSpinsCount);
    }
    
    notifyListeners();

    // Sync with Server
    final data = await _apiService.updateBalance(amount, source: source, gameId: gameId);
    
    // Update Limits from Server Response
    if (data.isNotEmpty) {
      if (data['games_played_today'] != null) {
        _gamesPlayedToday = int.tryParse(data['games_played_today'].toString()) ?? _gamesPlayedToday;
        await prefs.setInt('games_played_today', _gamesPlayedToday);
      }
      if (data['daily_limit'] != null) {
        _gameDailyLimit = int.tryParse(data['daily_limit'].toString()) ?? _gameDailyLimit;
      }
      
      if (data['ads_watched_today'] != null) {
        _adsWatchedToday = int.tryParse(data['ads_watched_today'].toString()) ?? _adsWatchedToday;
        await prefs.setInt('ads_watched_today', _adsWatchedToday);
      }
      if (data['ad_daily_limit'] != null) {
        _adDailyLimit = int.tryParse(data['ad_daily_limit'].toString()) ?? _adDailyLimit;
      }

      if (data['lucky_wheel_spins_today'] != null) {
        _luckyWheelSpinsCount = int.tryParse(data['lucky_wheel_spins_today'].toString()) ?? _luckyWheelSpinsCount;
        await prefs.setInt('lucky_wheel_spins_count', _luckyWheelSpinsCount);
      }
      if (data['lucky_wheel_limit'] != null) {
        _luckyWheelLimit = int.tryParse(data['lucky_wheel_limit'].toString()) ?? _luckyWheelLimit;
      }
      
      notifyListeners();
    }
  }

  Future<void> incrementWatchedAdsCount() async {
    _adsWatchedToday++;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setInt('ads_watched_today', _adsWatchedToday);
    notifyListeners();
    
    // Sync logic is handled via addCoins(source='ad_watch') which calls updateBalance
  }

  Future<void> incrementLuckyWheelSpinsCount() async {
    _luckyWheelSpinsCount++;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setInt('lucky_wheel_spins_count', _luckyWheelSpinsCount);
    notifyListeners();
  }

  Future<void> checkDailyLimitReset() async {
    final prefs = await SharedPreferences.getInstance();
    String today = DateTime.now().toIso8601String().split('T')[0];
    String? lastGameDate = prefs.getString('last_game_date');

    if (lastGameDate != today) {
      _gamesPlayedToday = 0;
      _adsWatchedToday = 0;
      _luckyWheelSpinsCount = 0;
      await prefs.setString('last_game_date', today);
      await prefs.setInt('games_played_today', 0);
      await prefs.setInt('ads_watched_today', 0);
      await prefs.setInt('lucky_wheel_spins_count', 0);
      notifyListeners();
    } else {
        // Load persisted values if same day
        _luckyWheelSpinsCount = prefs.getInt('lucky_wheel_spins_count') ?? 0;
    }
  }

  Future<void> incrementGamePlayCount() async {
    final prefs = await SharedPreferences.getInstance();
    String today = DateTime.now().toIso8601String().split('T')[0];
    String? lastGameDate = prefs.getString('last_game_date');

    // Check for day change before incrementing
    if (lastGameDate != today) {
      _gamesPlayedToday = 0;
      await prefs.setString('last_game_date', today);
    }

    _gamesPlayedToday++;
    await prefs.setInt('games_played_today', _gamesPlayedToday);
    notifyListeners();

    // Sync with Server
    await _apiService.incrementPlayCount();
  }

  // Admin Actions removed from Provider as they are now handled via Admin Panel
}
