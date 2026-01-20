import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../models/short_model.dart';
import '../config/api_config.dart';

class Comment {
  final int id;
  final String body;
  final String userName;
  final String? userImage;
  final String createdAt;

  Comment({
    required this.id,
    required this.body,
    required this.userName,
    this.userImage,
    required this.createdAt,
  });

  factory Comment.fromJson(Map<String, dynamic> json) {
    return Comment(
      id: json['id'],
      body: json['body'],
      userName: json['user'] != null ? json['user']['name'] ?? 'User' : 'User',
      userImage: json['user'] != null ? (json['user']['avatar'] ?? json['user']['image']) : null,
      createdAt: json['created_at'] ?? '',
    );
  }
}

class ShortsProvider extends ChangeNotifier {
  List<Short> _shorts = [];
  bool _isLoading = false;
  int _rewardTimer = 30; // Default
  int _rewardAmount = 10; // Default
  
  // Ad Fallbacks
  String _adFallback1 = 'admob';
  String _adFallback2 = 'applovin';
  String _adFallback3 = 'unity';

  List<Short> get shorts => _shorts;
  bool get isLoading => _isLoading;
  int get rewardTimer => _rewardTimer;
  int get rewardAmount => _rewardAmount;
  
  String get adFallback1 => _adFallback1;
  String get adFallback2 => _adFallback2;
  String get adFallback3 => _adFallback3;

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

  Future<void> fetchShorts() async {
    _isLoading = true;
    notifyListeners();

    final url = '${ApiConfig.apiUrl}/shorts';
    try {
      _logRequest('GET', url);
      final response = await http.get(
        Uri.parse(url), 
        headers: await _getHeaders(),
      );
      _logResponse('GET', url, response);

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        _shorts = (data['data'] as List).map((e) => Short.fromJson(e)).toList();
      }
    } catch (e) {
      _logError('GET', url, e);
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<void> fetchSettings() async {
    final url = '${ApiConfig.apiUrl}/settings/shorts';
    try {
      _logRequest('GET', url);
      final response = await http.get(
        Uri.parse(url),
        headers: await _getHeaders(),
      );
      _logResponse('GET', url, response);

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['success'] == true) {
          final settings = data['data'];
          if (settings['shorts_reward_timer'] != null) {
            _rewardTimer = int.parse(settings['shorts_reward_timer'].toString());
          }
          if (settings['shorts_reward_coins'] != null) {
            _rewardAmount = int.parse(settings['shorts_reward_coins'].toString());
          }
          
          _adFallback1 = settings['ad_fallback_1'] ?? 'admob';
          _adFallback2 = settings['ad_fallback_2'] ?? 'applovin';
          _adFallback3 = settings['ad_fallback_3'] ?? 'unity';
          
          notifyListeners();
        }
      }
    } catch (e) {
      _logError('GET', url, e);
    }
  }

  Future<bool> likeShort(String id) async {
    final url = '${ApiConfig.apiUrl}/shorts/$id/like';
    try {
      // Optimistic update
      final index = _shorts.indexWhere((s) => s.id == id);
      if (index != -1) {
        final short = _shorts[index];
        final newIsLiked = !short.isLiked;
        final newLikes = newIsLiked ? short.likes + 1 : short.likes - 1;
        
        _shorts[index] = Short(
          id: short.id,
          title: short.title,
          videoUrl: short.videoUrl,
          thumbnailUrl: short.thumbnailUrl,
          views: short.views,
          likes: newLikes,
          commentsCount: short.commentsCount,
          isLiked: newIsLiked,
        );
        notifyListeners();
      }

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
  
  Future<List<Comment>> fetchComments(String shortId) async {
    final url = '${ApiConfig.apiUrl}/shorts/$shortId/comments';
    try {
      _logRequest('GET', url);
      final response = await http.get(
        Uri.parse(url),
        headers: await _getHeaders(),
      );
      _logResponse('GET', url, response);
      
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        // Safely access nested data
        final commentsData = data['data'];
        final list = commentsData is Map && commentsData.containsKey('data') 
            ? commentsData['data'] 
            : commentsData;
            
        if (list is List) {
          return list.map((e) => Comment.fromJson(e)).toList();
        }
      }
    } catch (e) {
      _logError('GET', url, e);
    }
    return [];
  }
  
  Future<Comment?> postComment(String shortId, String body) async {
    final url = '${ApiConfig.apiUrl}/shorts/$shortId/comments';
    try {
      final requestBody = json.encode({'body': body});
      _logRequest('POST', url, body: requestBody);
      
      final response = await http.post(
        Uri.parse(url),
        headers: await _getHeaders(),
        body: requestBody,
      );
      _logResponse('POST', url, response);
      
      if (response.statusCode == 200 || response.statusCode == 201) {
        final data = json.decode(response.body);
        
        // Optimistic update for comments count
        final index = _shorts.indexWhere((s) => s.id == shortId);
        if (index != -1) {
          final short = _shorts[index];
          _shorts[index] = Short(
            id: short.id,
            title: short.title,
            videoUrl: short.videoUrl,
            thumbnailUrl: short.thumbnailUrl,
            views: short.views,
            likes: short.likes,
            commentsCount: short.commentsCount + 1,
            isLiked: short.isLiked,
          );
          notifyListeners();
        }

        return Comment.fromJson(data['data']);
      }
    } catch (e) {
      _logError('POST', url, e);
    }
    return null;
  }
  
  Future<bool> claimReward(String shortId) async {
    final url = '${ApiConfig.apiUrl}/shorts/$shortId/reward';
    try {
      _logRequest('POST', url);
      final response = await http.post(
        Uri.parse(url),
        headers: await _getHeaders(),
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
}
