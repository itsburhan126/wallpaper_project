import 'dart:convert';
import 'dart:io';
import 'package:flutter/foundation.dart' hide Category;
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config/api_config.dart';
import '../models/wallpaper_model.dart';
import '../models/category_model.dart';
import '../models/banner_model.dart';
import '../models/game_model.dart';
import '../models/support_ticket.dart';

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
      debugPrint('Error fetching general settings: $e');
    }
    return {};
  }

  Future<Map<String, dynamic>> getGameSettings() async {
    const url = "${ApiConfig.apiUrl}/settings/game";
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
      debugPrint('Error fetching game settings: $e');
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
      debugPrint('Error fetching ad settings: $e');
    }
    return {};
  }

  Future<Map<String, dynamic>> getSecuritySettings() async {
    const url = "${ApiConfig.apiUrl}/settings/security";
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
      debugPrint('Error fetching security settings: $e');
    }
    return {};
  }

  // Pages
  Future<Map<String, dynamic>?> getPage(String slug) async {
    final url = "${ApiConfig.apiUrl}/pages/$slug";
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
      debugPrint('Error fetching page: $e');
    }
    return null;
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
    debugPrint('🔵 [API Request] $method: $url');
    if (body != null) debugPrint('📦 Body: $body');
  }

  void _logResponse(String method, String url, http.Response response) {
    debugPrint(response.statusCode >= 200 && response.statusCode < 300 
        ? '🟢 [API Success] $method: $url' 
        : '🔴 [API Error ${response.statusCode}] $method: $url');
    debugPrint('📄 Response: ${response.body}');
  }

  void _logError(String method, String url, dynamic error) {
    debugPrint('❌ [API Exception] $method: $url');
    debugPrint('⚠️ Error: $error');
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
          debugPrint('🎮 Games Data Structure: ${data['data'].runtimeType}');
          
          if (data['data'] is Map && data['data'].containsKey('data')) {
             // Pagination structure
             final List gamesData = data['data']['data'];
             debugPrint('🎮 Found ${gamesData.length} games in pagination');
             return gamesData.map((e) => GameModel.fromJson(e)).toList();
          } else if (data['data'] is List) {
             // Direct list structure
             final List gamesData = data['data'];
             debugPrint('🎮 Found ${gamesData.length} games in list');
             return gamesData.map((e) => GameModel.fromJson(e)).toList();
          }
        }
      }
    } catch (e, stackTrace) {
      _logError('GET', url, e);
      debugPrint('Stack trace: $stackTrace');
    }
    return [];
  }

  // Auth Methods
  Future<Map<String, dynamic>> login(String email, String password, {String? deviceId}) async {
    const url = ApiConfig.login;
    try {
      final body = {
        'email': email, 
        'password': password,
        if (deviceId != null) 'device_id': deviceId,
      };
      _logRequest('POST', url, body: json.encode(body));
      
      final response = await http.post(
        Uri.parse(url),
        headers: await _getHeaders(),
        body: json.encode(body),
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
      return {'success': false, 'message': 'Connection error'};
    }
  }

  Future<bool> deleteAccount() async {
    const url = "${ApiConfig.apiUrl}/delete-account";
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

  // Support
  Future<List<SupportTicket>> getSupportTickets() async {
    const url = "${ApiConfig.apiUrl}/support/tickets";
    try {
      _logRequest('GET', url);
      final response = await http.get(Uri.parse(url), headers: await _getHeaders());
      _logResponse('GET', url, response);

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['status'] == true) {
          final List ticketsData = data['data'];
          return ticketsData.map((e) => SupportTicket.fromJson(e)).toList();
        }
      }
    } catch (e) {
      _logError('GET', url, e);
    }
    return [];
  }

  Future<bool> createSupportTicket(String subject, String message) async {
    const url = "${ApiConfig.apiUrl}/support/tickets";
    try {
      final body = json.encode({
        'subject': subject,
        'message': message,
        'priority': 'low', // Default
      });
      _logRequest('POST', url, body: body);
      
      final response = await http.post(
        Uri.parse(url),
        headers: await _getHeaders(),
        body: body,
      );
      _logResponse('POST', url, response);

      if (response.statusCode == 201) {
        return true;
      }
    } catch (e) {
      _logError('POST', url, e);
    }
    return false;
  }

  Future<SupportTicket?> getSupportTicket(int id) async {
    final url = "${ApiConfig.apiUrl}/support/tickets/$id";
    try {
      _logRequest('GET', url);
      final response = await http.get(Uri.parse(url), headers: await _getHeaders());
      _logResponse('GET', url, response);

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['status'] == true) {
          return SupportTicket.fromJson(data['data']);
        }
      }
    } catch (e) {
      _logError('GET', url, e);
    }
    return null;
  }

  Future<bool> replyToTicket(int id, String message) async {
    final url = "${ApiConfig.apiUrl}/support/tickets/$id/reply";
    try {
      final body = json.encode({'message': message});
      _logRequest('POST', url, body: body);
      
      final response = await http.post(
        Uri.parse(url),
        headers: await _getHeaders(),
        body: body,
      );
      _logResponse('POST', url, response);

      if (response.statusCode == 200) {
        return true;
      }
    } catch (e) {
      _logError('POST', url, e);
    }
    return false;
  }

  // Redeem & History
  Future<List<dynamic>> getCoinHistory() async {
    const url = "${ApiConfig.apiUrl}/transactions";
    try {
      _logRequest('GET', url);
      final response = await http.get(Uri.parse(url), headers: await _getHeaders());
      _logResponse('GET', url, response);

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['status'] == true) {
          return data['data']; // Assuming data is a list of transactions
        }
      }
    } catch (e) {
      _logError('GET', url, e);
    }
    return [];
  }

  Future<List<dynamic>> getRedeemHistory() async {
    const url = "${ApiConfig.apiUrl}/redeem/history";
    try {
      _logRequest('GET', url);
      final response = await http.get(Uri.parse(url), headers: await _getHeaders());
      _logResponse('GET', url, response);

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['status'] == true) {
          return data['data']; // Assuming data is a list of redeem requests
        }
      }
    } catch (e) {
      _logError('GET', url, e);
    }
    return [];
  }

  Future<List<dynamic>> getRedeemGateways() async {
    const url = "${ApiConfig.apiUrl}/redeem/gateways";
    try {
      _logRequest('GET', url);
      final response = await http.get(Uri.parse(url), headers: await _getHeaders());
      _logResponse('GET', url, response);

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['status'] == true) {
          return data['data'];
        }
      }
    } catch (e) {
      _logError('GET', url, e);
    }
    return [];
  }

  Future<List<dynamic>> getRedeemMethods(int gatewayId) async {
    final url = "${ApiConfig.apiUrl}/redeem/methods/$gatewayId";
    try {
      _logRequest('GET', url);
      final response = await http.get(Uri.parse(url), headers: await _getHeaders());
      _logResponse('GET', url, response);

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['status'] == true) {
          return data['data'];
        }
      }
    } catch (e) {
      _logError('GET', url, e);
    }
    return [];
  }

  Future<Map<String, dynamic>> submitRedeemRequest(int methodId, String accountDetails, num amount) async {
    const url = "${ApiConfig.apiUrl}/redeem/request";
    try {
      final body = json.encode({
        'method_id': methodId,
        'account_details': accountDetails,
        'amount': amount,
      });
      _logRequest('POST', url, body: body);

      final response = await http.post(
        Uri.parse(url),
        headers: await _getHeaders(),
        body: body,
      );
      _logResponse('POST', url, response);

      final data = json.decode(response.body);
      return data;
    } catch (e) {
      _logError('POST', url, e);
      return {'success': false, 'message': 'Connection error'};
    }
  }

  Future<Map<String, dynamic>> register(String name, String email, String password, {String? referralCode, String? deviceId}) async {
    const url = ApiConfig.register;
    final body = {
      'name': name,
      'email': email,
      'password': password,
      if (referralCode != null && referralCode.isNotEmpty) 'referral_code': referralCode,
      if (deviceId != null) 'device_id': deviceId,
    };
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

  Future<Map<String, dynamic>> googleLogin(String email, String name, String googleId, String? avatar, {String? deviceId}) async {
    const url = ApiConfig.googleLogin;
    final body = {
      'email': email,
      'name': name,
      'google_id': googleId,
      if (avatar != null) 'avatar': avatar,
      if (deviceId != null) 'device_id': deviceId,
    };
    try {
      _logRequest('POST', url, body: body);
      final response = await http.post(
        Uri.parse(url),
        headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
        body: json.encode(body),
      );
      _logResponse('POST', url, response);

      final data = json.decode(response.body);
      if (response.statusCode == 200 && data['status'] == true) {
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('auth_token', data['data']['token']);
        return {'success': true, 'data': data['data']};
      } else {
        return {'success': false, 'message': data['message'] ?? 'Google Login failed'};
      }
    } catch (e) {
      _logError('POST', url, e);
      return {'success': false, 'message': 'Connection error: $e'};
    }
  }

  Future<Map<String, dynamic>> addReferrer(String referralCode) async {
    const url = ApiConfig.addReferrer;
    final body = {'referral_code': referralCode};
    try {
      _logRequest('POST', url, body: body);
      final response = await http.post(
        Uri.parse(url),
        headers: await _getHeaders(),
        body: json.encode(body),
      );
      _logResponse('POST', url, response);

      final data = json.decode(response.body);
      if (response.statusCode == 200 && data['status'] == true) {
        return {'success': true, 'message': data['message']};
      } else {
        return {'success': false, 'message': data['message'] ?? 'Failed to add referrer'};
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

  // Profile Methods
  Future<Map<String, dynamic>> updateProfile(String name) async {
    const url = ApiConfig.updateProfile;
    final body = {'name': name};
    try {
      _logRequest('POST', url, body: body);
      final response = await http.post(
        Uri.parse(url),
        headers: await _getHeaders(),
        body: json.encode(body),
      );
      _logResponse('POST', url, response);

      final data = json.decode(response.body);
      if (response.statusCode == 200 && data['status'] == true) {
        return {'success': true, 'data': data['data'], 'message': data['message']};
      } else {
        return {'success': false, 'message': data['message'] ?? 'Update failed'};
      }
    } catch (e) {
      _logError('POST', url, e);
      return {'success': false, 'message': 'Connection error: $e'};
    }
  }

  Future<Map<String, dynamic>> changePassword(String currentPassword, String newPassword) async {
    const url = ApiConfig.changePassword;
    final body = {
      'current_password': currentPassword,
      'new_password': newPassword,
      'new_password_confirmation': newPassword,
    };
    try {
      _logRequest('POST', url, body: body);
      final response = await http.post(
        Uri.parse(url),
        headers: await _getHeaders(),
        body: json.encode(body),
      );
      _logResponse('POST', url, response);

      final data = json.decode(response.body);
      if (response.statusCode == 200 && data['status'] == true) {
        return {'success': true, 'message': data['message']};
      } else {
        return {'success': false, 'message': data['message'] ?? 'Password change failed'};
      }
    } catch (e) {
      _logError('POST', url, e);
      return {'success': false, 'message': 'Connection error: $e'};
    }
  }

  Future<Map<String, dynamic>> updateAvatar(File imageFile) async {
    const url = ApiConfig.updateAvatar;
    try {
      _logRequest('POST (Multipart)', url);
      final request = http.MultipartRequest('POST', Uri.parse(url));
      request.headers.addAll(await _getHeaders());
      
      request.files.add(await http.MultipartFile.fromPath('avatar', imageFile.path));
      
      final streamedResponse = await request.send();
      final response = await http.Response.fromStream(streamedResponse);
      
      _logResponse('POST', url, response);

      final data = json.decode(response.body);
      if (response.statusCode == 200 && data['status'] == true) {
        return {'success': true, 'data': data['data'], 'message': data['message']};
      } else {
        return {'success': false, 'message': data['message'] ?? 'Avatar update failed'};
      }
    } catch (e) {
      _logError('POST', url, e);
      return {'success': false, 'message': 'Connection error: $e'};
    }
  }

  Future<Map<String, dynamic>> updateBalance(int amount, {String source = 'app_activity', String? gameId}) async {
    const url = ApiConfig.updateBalance;
    final body = {
          'amount': amount,
          'type': 'coin',
          'source': source,
          if (gameId != null) 'game_id': gameId,
        };
    try {
      _logRequest('POST', url, body: body);
      final response = await http.post(
        Uri.parse(url),
        headers: await _getHeaders(),
        body: json.encode(body),
      );
      _logResponse('POST', url, response);
      
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['status'] == true) {
             return data['data'];
        }
      }
    } catch (e) {
      _logError('POST', url, e);
    }
    return {};
  }

  Future<Map<String, dynamic>> fetchGameStatus() async {
    const url = ApiConfig.gameStatus;
    try {
      _logRequest('GET', url);
      final response = await http.get(Uri.parse(url), headers: await _getHeaders());
      _logResponse('GET', url, response);
      
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['status'] == true) {
             return data['data'];
        }
      }
    } catch (e) {
      _logError('GET', url, e);
    }
    return {};
  }

  Future<bool> incrementPlayCount() async {
    const url = ApiConfig.incrementPlayCount;
    try {
      _logRequest('POST', url);
      final response = await http.post(
        Uri.parse(url),
        headers: await _getHeaders(),
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
          title: e['title'],
          imageUrl: e['image_url'] ?? e['image'],
          linkUrl: e['link'],
        )).toList();
      }
    } catch (e) {
      _logError('GET', url, e);
      debugPrint('Error fetching banners: $e');
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
      debugPrint('Error fetching categories: $e');
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
      debugPrint('Error fetching wallpapers: $e');
    }
    return [];
  }

  // Increment Wallpaper View
  Future<bool> viewWallpaper(String id) async {
    final url = "${ApiConfig.wallpapers}/$id";
    try {
      _logRequest('GET', url);
      final response = await http.get(Uri.parse(url));
      _logResponse('GET', url, response);
      return response.statusCode == 200;
    } catch (e) {
      _logError('GET', url, e);
      return false;
    }
  }

  // Increment Wallpaper Download
  Future<bool> downloadWallpaper(String id) async {
    final url = "${ApiConfig.wallpapers}/$id/download";
    try {
      _logRequest('POST', url);
      final response = await http.post(Uri.parse(url));
      _logResponse('POST', url, response);
      return response.statusCode == 200;
    } catch (e) {
      _logError('POST', url, e);
      return false;
    }
  }
}
