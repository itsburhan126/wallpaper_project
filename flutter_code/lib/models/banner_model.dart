class BannerModel {
  final String id;
  final String? title;
  final String imageUrl;
  final String? linkUrl;

  BannerModel({
    required this.id,
    this.title,
    required this.imageUrl,
    this.linkUrl,
  });
}
