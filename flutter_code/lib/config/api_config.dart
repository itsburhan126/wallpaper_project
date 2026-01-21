class ApiConfig {
  // Replace with your local IP or domain if testing on real device
  // For Android Emulator: 10.0.2.2
  // For iOS Simulator: 127.0.0.1
  static const String baseUrl = "http://192.168.1.120:8000"; 
  static const String apiUrl = "$baseUrl/api";
  
  static const String banners = "$apiUrl/banners";
  static const String categories = "$apiUrl/categories";
  static const String wallpapers = "$apiUrl/wallpapers";
  
  static const String login = "$apiUrl/login";
  static const String googleLogin = "$apiUrl/auth/google";
  static const String addReferrer = "$apiUrl/add-referrer";
  static const String register = "$apiUrl/register";
  static const String user = "$apiUrl/user";
  static const String games = "$apiUrl/games";
  static const String updateBalance = "$apiUrl/game/update-balance";
  static const String incrementPlayCount = "$apiUrl/game/increment-play-count";
  static const String gameStatus = "$apiUrl/game/status";
}
