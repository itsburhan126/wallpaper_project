class Short {
  final String id;
  final String title;
  final String videoUrl;
  final String? thumbnailUrl;
  final int views;
  final int likes;
  final int commentsCount;
  final bool isLiked;

  Short({
    required this.id,
    required this.title,
    required this.videoUrl,
    this.thumbnailUrl,
    this.views = 0,
    this.likes = 0,
    this.commentsCount = 0,
    this.isLiked = false,
  });

  factory Short.fromJson(Map<String, dynamic> json) {
    return Short(
      id: json['id'].toString(),
      title: json['title'] ?? '',
      videoUrl: json['video_url'] ?? '',
      thumbnailUrl: json['thumbnail_url'],
      views: json['views_count'] ?? json['views'] ?? 0,
      likes: json['likes_count'] ?? json['likes'] ?? 0,
      commentsCount: json['comments_count'] ?? 0,
      isLiked: json['is_liked'] ?? false,
    );
  }
}
