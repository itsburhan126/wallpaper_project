import 'package:flutter/material.dart';

class MockAdService {
  static void showRewardedAd(BuildContext context, VoidCallback onReward) {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) {
        return _MockAdDialog(onReward: onReward);
      },
    );
  }
}

class _MockAdDialog extends StatefulWidget {
  final VoidCallback onReward;

  const _MockAdDialog({required this.onReward});

  @override
  State<_MockAdDialog> createState() => _MockAdDialogState();
}

class _MockAdDialogState extends State<_MockAdDialog> {
  int _secondsRemaining = 5;

  @override
  void initState() {
    super.initState();
    _startTimer();
  }

  void _startTimer() {
    Future.delayed(const Duration(seconds: 1), () {
      if (!mounted) return;
      setState(() {
        if (_secondsRemaining > 0) {
          _secondsRemaining--;
          _startTimer();
        } else {
          // Ad finished
        }
      });
    });
  }

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: false, // Prevent closing
      child: Dialog(
        backgroundColor: Colors.black,
        insetPadding: EdgeInsets.zero,
        child: SizedBox.expand(
          child: Stack(
            children: [
              Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Icon(Icons.play_circle_fill, color: Colors.white, size: 80),
                    const SizedBox(height: 20),
                    const Text(
                      'ADVERTISEMENT',
                      style: TextStyle(color: Colors.white, fontSize: 24, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 10),
                    Text(
                      'Reward in $_secondsRemaining seconds',
                      style: const TextStyle(color: Colors.grey, fontSize: 16),
                    ),
                  ],
                ),
              ),
              if (_secondsRemaining == 0)
                Positioned(
                  top: 40,
                  right: 20,
                  child: IconButton(
                    icon: const Icon(Icons.close, color: Colors.white, size: 30),
                    onPressed: () {
                      Navigator.of(context).pop();
                      widget.onReward();
                    },
                  ),
                ),
            ],
          ),
        ),
      ),
    );
  }
}
