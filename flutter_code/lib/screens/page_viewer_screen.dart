import 'package:flutter/material.dart';
import 'package:webview_flutter/webview_flutter.dart';
import 'package:google_fonts/google_fonts.dart';
import '../services/api_service.dart';
import '../utils/app_theme.dart';

class PageViewerScreen extends StatefulWidget {
  final String slug;
  final String title;

  const PageViewerScreen({
    super.key,
    required this.slug,
    required this.title,
  });

  @override
  State<PageViewerScreen> createState() => _PageViewerScreenState();
}

class _PageViewerScreenState extends State<PageViewerScreen> {
  late WebViewController _controller;
  bool _isLoading = true;
  final ApiService _apiService = ApiService();

  @override
  void initState() {
    super.initState();
    _controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setBackgroundColor(const Color(0x00000000))
      ..setNavigationDelegate(
        NavigationDelegate(
          onPageStarted: (String url) {},
          onPageFinished: (String url) {},
          onWebResourceError: (WebResourceError error) {},
        ),
      );
    _loadPageContent();
  }

  Future<void> _loadPageContent() async {
    final pageData = await _apiService.getPage(widget.slug);
    if (pageData != null && mounted) {
      final content = pageData['content'] ?? '<p>No content available.</p>';
      
      // Wrap content with basic HTML structure and styling for dark mode
      final htmlContent = '''
        <!DOCTYPE html>
        <html>
        <head>
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <style>
            @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
            body {
              background-color: #0f172a; /* Match AppTheme.darkBackgroundColor roughly */
              color: #e2e8f0;
              font-family: 'Poppins', sans-serif;
              padding: 16px;
              line-height: 1.6;
            }
            h1, h2, h3, h4, h5, h6 {
              color: #f8fafc;
              margin-top: 24px;
              margin-bottom: 12px;
            }
            a { color: #6366f1; }
            strong { color: #f1f5f9; }
            ul, ol { padding-left: 20px; }
            li { margin-bottom: 8px; }
          </style>
        </head>
        <body>
          $content
        </body>
        </html>
      ''';
      
      _controller.loadHtmlString(htmlContent);
      setState(() {
        _isLoading = false;
      });
    } else if (mounted) {
       setState(() {
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.darkBackgroundColor,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        title: Text(
          widget.title,
          style: GoogleFonts.poppins(
            color: Colors.white,
            fontWeight: FontWeight.w600,
          ),
        ),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_rounded, color: Colors.white),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Colors.white))
          : WebViewWidget(controller: _controller),
    );
  }
}
