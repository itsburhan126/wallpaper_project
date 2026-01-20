import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config/api_config.dart';
import '../models/wallpaper_model.dart';
import '../models/category_model.dart';
import '../models/game_model.dart';
import '../models/banner_model.dart';

class ApiService {
  Future<String?> _getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('auth_token');
  }

  Future<Map<String, String>> _getHeaders() async {
    final token = await _getToken();
    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      if (token != null) 'Authorization': 'Bearer $token',
    };
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

  // Fetch Games
  Future<List<Game>> getGames() async {
    const url = ApiConfig.games;
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
        
        return list.map((e) => Game(
          id: e['id'].toString(),
          title: e['title'],
          url: e['link'] ?? e['url'],
          thumbUrl: e['image_url'] ?? e['image'],
          playTimeSeconds: e['play_time'] ?? 30, // Default 30s if null
        )).toList();
      }
    } catch (e) {
      _logError('GET', url, e);
      print('Error fetching games: $e');
    }
    return [];
  }

  // Fetch Wallpapers
  Future<List<Wallpaper>> getWallpapers() async {
    const url = ApiConfig.wallpapers;
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
