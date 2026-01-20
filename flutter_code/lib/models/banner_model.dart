class BannerModel {
  final String id;
  final String imageUrl;
  final String? linkUrl;

  BannerModel({
    required this.id,
    required this.imageUrl,
    this.linkUrl,
  });
}
