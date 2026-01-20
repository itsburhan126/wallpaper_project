import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config/api_config.dart';
import '../models/wallpaper_model.dart';
import '../models/category_model.dart';
import '../models/banner_model.dart';
import '../models/game_model.dart';

class ApiService {
  Future<String?> _getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('auth_token');
  }

  Future<Map<String, dynamic>> getGeneralSettings() async {
    const url = "${ApiConfig.apiUrl}/settings/general";
    try {
      _logRequest('GET', url);
      final response = await http.get(Uri.parse(url));
      _logResponse('GET', url, response);

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['success'] == true) {
          return data['data'];
        }
      }
    } catch (e) {
      _logError('GET', url, e);
      print('Error fetching general settings: $e');
    }
    return {};
  }

  Future<Map<String, dynamic>> getAdSettings() async {
    const url = "${ApiConfig.apiUrl}/settings/ads";
    try {
      _logRequest('GET', url);
      final response = await http.get(Uri.parse(url));
      _logResponse('GET', url, response);

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['success'] == true) {
          return data['data'];
        }
      }
    } catch (e) {
      _logError('GET', url, e);
      print('Error fetching ad settings: $e');
    }
    return {};
  }

  Future<Map<String, String>> _getHeaders() async {
    final token = await _getToken();
    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      if (token != null) 'Authorization': 'Bearer $token',
    };
  }

  // Daily Rewards
  Future<Map<String, dynamic>> fetchDailyRewards() async {
    const url = "${ApiConfig.apiUrl}/daily-rewards";
    try {
      _logRequest('GET', url);
      final response = await http.get(Uri.parse(url), headers: await _getHeaders());
      _logResponse('GET', url, response);

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        // Controller returns 'status', but we check both just in case
        if (data['status'] == true || data['success'] == true) {
          return data['data'];
        }
      }
    } catch (e) {
      _logError('GET', url, e);
    }
    return {};
  }

  Future<bool> claimDailyReward() async {
    const url = "${ApiConfig.apiUrl}/daily-rewards/claim";
    try {
      _logRequest('POST', url);
      final response = await http.post(
        Uri.parse(url), 
        headers: await _getHeaders(),
      );
      _logResponse('POST', url, response);

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        return data['status'] == true || data['success'] == true;
      }
    } catch (e) {
      _logError('POST', url, e);
    }
    return false;
  }

  // Logging Helpers
  void _logRequest(String method, String url, {dynamic body}) {
    print('🔵 [API Request] $method: $url');
    if (body != null) print('📦 Body: $body');
  }

  void _logResponse(String method, String url, http.Response response) {
    print(response.statusCode >= 200 && response.statusCode < 300 
        ? '🟢 [API Success] $method: $url' 
        : '🔴 [API Error ${response.statusCode}] $method: $url');
    print('📄 Response: ${response.body}');
  }

  void _logError(String method, String url, dynamic error) {
    print('❌ [API Exception] $method: $url');
    print('⚠️ Error: $error');
  }

  Future<List<GameModel>> getGames() async {
    const url = ApiConfig.games;
    try {
      _logRequest('GET', url);
      final response = await http.get(Uri.parse(url), headers: await _getHeaders());
      _logResponse('GET', url, response);

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['status'] == true) {
          // Debugging: Print structure
          print('🎮 Games Data Structure: ${data['data'].runtimeType}');
          
          if (data['data'] is Map && data['data'].containsKey('data')) {
             // Pagination structure
             final List gamesData = data['data']['data'];
             print('🎮 Found ${gamesData.length} games in pagination');
             return gamesData.map((e) => GameModel.fromJson(e)).toList();
          } else if (data['data'] is List) {
             // Direct list structure
             final List gamesData = data['data'];
             print('🎮 Found ${gamesData.length} games in list');
             return gamesData.map((e) => GameModel.fromJson(e)).toList();
          }
        }
      }
    } catch (e, stackTrace) {
      _logError('GET', url, e);
      print('Stack trace: $stackTrace');
    }
    return [];
  }

  // Auth Methods
  Future<Map<String, dynamic>> login(String email, String password) async {
    const url = ApiConfig.login;
    try {
      final body = json.encode({'email': email, 'password': password});
      _logRequest('POST', url, body: body);
      
      final response = await http.post(
        Uri.parse(url),
        headers: await _getHeaders(),
        body: body,
      );
      
      _logResponse('POST', url, response);

      final data = json.decode(response.body);
      if (response.statusCode == 200 && data['status'] == true) {
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('auth_token', data['data']['token']);
        // Save user info if needed
        return {'success': true, 'data': data['data']};
      } else {
        return {'success': false, 'message': data['message'] ?? 'Login failed'};
      }
    } catch (e) {
      _logError('POST', url, e);
      return {'success': false, 'message': 'Connection error: $e'};
    }
  }

  Future<Map<String, dynamic>> register(String name, String email, String password) async {
    const url = ApiConfig.register;
    final body = {'name': name, 'email': email, 'password': password};
    try {
      _logRequest('POST', url, body: body);
      final response = await http.post(
        Uri.parse(url),
        headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
        body: json.encode(body),
      );
      _logResponse('POST', url, response);

      final data = json.decode(response.body);
      if (response.statusCode == 201 && data['status'] == true) {
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('auth_token', data['data']['token']);
        return {'success': true, 'data': data['data']};
      } else {
        return {'success': false, 'message': data['message'] ?? 'Registration failed'};
      }
    } catch (e) {
      _logError('POST', url, e);
      return {'success': false, 'message': 'Connection error: $e'};
    }
  }

  Future<Map<String, dynamic>> getUserDetails() async {
    const url = ApiConfig.user;
    try {
      _logRequest('GET', url);
      final response = await http.get(Uri.parse(url), headers: await _getHeaders());
      _logResponse('GET', url, response);

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['status'] == true) {
          return {'success': true, 'data': data['data']};
        }
      }
    } catch (e) {
      _logError('GET', url, e);
    }
    return {'success': false};
  }

  Future<bool> updateBalance(int amount) async {
    const url = ApiConfig.updateBalance;
    final body = {
          'amount': amount,
          'type': 'coin',
          'source': 'app_activity'
        };
    try {
      _logRequest('POST', url, body: body);
      final response = await http.post(
        Uri.parse(url),
        headers: await _getHeaders(),
        body: json.encode(body),
      );
      _logResponse('POST', url, response);
      return response.statusCode == 200;
    } catch (e) {
      _logError('POST', url, e);
      return false;
    }
  }

  // Fetch Banners
  Future<List<BannerModel>> getBanners() async {
    const url = ApiConfig.banners;
    try {
      _logRequest('GET', url);
      final response = await http.get(Uri.parse(url));
      _logResponse('GET', url, response);

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        // Assuming API returns { data: [...] } or just [...]
        final List<dynamic> list = data['data'] ?? data;
        return list.map((e) => BannerModel(
          id: e['id'].toString(),
          imageUrl: e['image_url'] ?? e['image'],
          linkUrl: e['link'],
        )).toList();
      }
    } catch (e) {
      _logError('GET', url, e);
      print('Error fetching banners: $e');
    }
    return [];
  }

  // Fetch Categories
  Future<List<Category>> getCategories() async {
    const url = ApiConfig.categories;
    try {
      _logRequest('GET', url);
      final response = await http.get(Uri.parse(url));
      _logResponse('GET', url, response);

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        final List<dynamic> list = data['data'] ?? data;
        return list.map((e) => Category(
          id: e['id'].toString(),
          name: e['name'],
          coverUrl: e['image_url'] ?? e['image'], // Adjust key based on API
        )).toList();
      }
    } catch (e) {
      _logError('GET', url, e);
      print('Error fetching categories: $e');
    }
    return [];
  }

  // Fetch Wallpapers
  Future<List<Wallpaper>> getWallpapers({String? categoryId, bool isHot = false}) async {
    String url = "${ApiConfig.wallpapers}?";
    if (categoryId != null) url += "category_id=$categoryId&";
    if (isHot) url += "featured=1&";

    try {
      _logRequest('GET', url);
      final response = await http.get(Uri.parse(url));
      _logResponse('GET', url, response);

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        var responseData = data['data'];
        // Handle pagination
        if (responseData is Map && responseData.containsKey('data')) {
          responseData = responseData['data'];
        }
        final List<dynamic> list = responseData ?? [];

        return list.map((e) => Wallpaper(
          id: e['id'].toString(),
          url: e['image'] ?? e['image_url'],
          thumbUrl: e['thumbnail'] ?? e['thumb_url'] ?? e['image'] ?? e['image_url'],
          category: e['category']?['name'] ?? 'Unknown',
        )).toList();
      }
    } catch (e) {
      _logError('GET', url, e);
      print('Error fetching wallpapers: $e');
    }
    return [];
  }
}
