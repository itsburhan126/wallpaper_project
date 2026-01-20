class GameModel {
  final int id;
  final String title;
  final String description;
  final String image;
  final String url;
  final int winReward;
  final bool isFeatured;

  GameModel({
    required this.id,
    required this.title,
    required this.description,
    required this.image,
    required this.url,
    required this.winReward,
    required this.isFeatured,
  });

  factory GameModel.fromJson(Map<String, dynamic> json) {
    return GameModel(
      id: json['id'] ?? 0,
      title: json['title'] ?? 'Unknown Game',
      description: json['description'] ?? '',
      image: json['image'] ?? '',
      url: json['url'] ?? '',
      winReward: int.tryParse(json['win_reward'].toString()) ?? 0,
      isFeatured: json['is_featured'] == 1 || json['is_featured'] == true,
    );
  }
}
