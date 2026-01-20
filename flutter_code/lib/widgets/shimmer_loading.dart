import 'package:flutter/material.dart';
import 'package:shimmer/shimmer.dart';

class ShimmerLoading extends StatelessWidget {
  final double width;
  final double height;
  final ShapeBorder shapeBorder;

  const ShimmerLoading.rectangular({
    super.key,
    this.width = double.infinity,
    required this.height,
    this.shapeBorder = const RoundedRectangleBorder(
        borderRadius: BorderRadius.all(Radius.circular(16))),
  });

  const ShimmerLoading.circular({
    super.key,
    this.width = double.infinity,
    required this.height,
    this.shapeBorder = const CircleBorder(),
  });

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: Colors.grey[900]!,
      highlightColor: Colors.grey[800]!,
      period: const Duration(milliseconds: 1500),
      child: Container(
        width: width,
        height: height,
        decoration: ShapeDecoration(
          color: Colors.grey[900]!,
          shape: shapeBorder,
        ),
      ),
    );
  }
}

class WallpaperShimmerGrid extends StatelessWidget {
  final int itemCount;
  final bool isSliver;

  const WallpaperShimmerGrid({
    super.key, 
    this.itemCount = 12,
    this.isSliver = false,
  });

  Widget _buildGrid() {
    return GridView.builder(
      physics: const NeverScrollableScrollPhysics(),
      shrinkWrap: true,
      padding: const EdgeInsets.all(12),
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 3,
        childAspectRatio: 0.55,
        crossAxisSpacing: 10,
        mainAxisSpacing: 10,
      ),
      itemCount: itemCount,
      itemBuilder: (context, index) {
        return const ShimmerLoading.rectangular(height: double.infinity);
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    if (isSliver) {
      return SliverPadding(
        padding: const EdgeInsets.all(12),
        sliver: SliverGrid(
          gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: 3,
            childAspectRatio: 0.55,
            crossAxisSpacing: 10,
            mainAxisSpacing: 10,
          ),
          delegate: SliverChildBuilderDelegate(
            (context, index) {
              return const ShimmerLoading.rectangular(height: double.infinity);
            },
            childCount: itemCount,
          ),
        ),
      );
    }
    return _buildGrid();
  }
}

class BannerShimmer extends StatelessWidget {
  const BannerShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 10),
      child: ShimmerLoading.rectangular(height: 220, width: MediaQuery.of(context).size.width * 0.92),
    );
  }
}

class CategoryShimmerGrid extends StatelessWidget {
  final int itemCount;

  const CategoryShimmerGrid({super.key, this.itemCount = 8});

  @override
  Widget build(BuildContext context) {
    return GridView.builder(
      padding: const EdgeInsets.all(16),
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 2,
        childAspectRatio: 0.8,
        crossAxisSpacing: 16,
        mainAxisSpacing: 16,
      ),
      itemCount: itemCount,
      itemBuilder: (context, index) {
        return const ShimmerLoading.rectangular(
          height: double.infinity,
          shapeBorder: RoundedRectangleBorder(
            borderRadius: BorderRadius.all(Radius.circular(24)),
          ),
        );
      },
    );
  }
}

class CategoryShimmerList extends StatelessWidget {
  final int itemCount;

  const CategoryShimmerList({super.key, this.itemCount = 8});

  @override
  Widget build(BuildContext context) {
    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: itemCount,
      itemBuilder: (context, index) {
        return Padding(
          padding: const EdgeInsets.only(bottom: 16),
          child: Row(
            children: [
              const ShimmerLoading.rectangular(height: 80, width: 80),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: const [
                    ShimmerLoading.rectangular(height: 20, width: 150),
                    SizedBox(height: 8),
                    ShimmerLoading.rectangular(height: 14, width: 100),
                  ],
                ),
              ),
            ],
          ),
        );
      },
    );
  }
}
