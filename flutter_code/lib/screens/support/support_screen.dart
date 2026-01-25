import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:flutter_animate/flutter_animate.dart';
import '../../models/support_ticket.dart';
import '../../providers/language_provider.dart';
import '../../services/api_service.dart';
import '../../utils/app_theme.dart';
import 'create_ticket_screen.dart';
import 'ticket_chat_screen.dart';

class SupportScreen extends StatefulWidget {
  const SupportScreen({super.key});

  @override
  State<SupportScreen> createState() => _SupportScreenState();
}

class _SupportScreenState extends State<SupportScreen> {
  late Future<List<SupportTicket>> _ticketsFuture;

  @override
  void initState() {
    super.initState();
    _refreshTickets();
  }

  void _refreshTickets() {
    setState(() {
      _ticketsFuture = ApiService().getSupportTickets();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Consumer<LanguageProvider>(
      builder: (context, languageProvider, _) {
        return Scaffold(
          backgroundColor: AppTheme.darkBackgroundColor,
          appBar: AppBar(
            backgroundColor: AppTheme.darkBackgroundColor,
            elevation: 0,
            title: Text(
              languageProvider.getText('help_center'),
              style: GoogleFonts.poppins(color: Colors.white, fontWeight: FontWeight.bold),
            ),
            leading: IconButton(
              icon: const Icon(Icons.arrow_back_ios, color: Colors.white),
              onPressed: () => Navigator.pop(context),
            ),
          ),
          body: FutureBuilder<List<SupportTicket>>(
            future: _ticketsFuture,
            builder: (context, snapshot) {
              if (snapshot.connectionState == ConnectionState.waiting) {
                return const Center(child: CircularProgressIndicator());
              }

              if (snapshot.hasError) {
                return Center(
                  child: Text(
                    "Error loading tickets",
                    style: GoogleFonts.poppins(color: Colors.white70),
                  ),
                );
              }

              final tickets = snapshot.data ?? [];

              if (tickets.isEmpty) {
                return Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(Icons.help_outline, size: 80, color: Colors.white.withOpacity(0.2)),
                      const SizedBox(height: 16),
                      Text(
                        "No tickets yet",
                        style: GoogleFonts.poppins(color: Colors.white70, fontSize: 16),
                      ),
                      const SizedBox(height: 8),
                      Text(
                        "Tap + to create a new support request",
                        style: GoogleFonts.poppins(color: Colors.white38, fontSize: 12),
                      ),
                    ],
                  ),
                );
              }

              return ListView.builder(
                padding: const EdgeInsets.all(16),
                itemCount: tickets.length,
                itemBuilder: (context, index) {
                  final ticket = tickets[index];
                  return _buildTicketCard(context, ticket)
                      .animate()
                      .fadeIn(duration: 400.ms, delay: (50 * index).ms)
                      .slideX(begin: 0.1, curve: Curves.easeOutQuad);
                },
              );
            },
          ),
          floatingActionButton: FloatingActionButton(
            backgroundColor: AppTheme.primaryColor,
            onPressed: () async {
              final result = await Navigator.push(
                context,
                MaterialPageRoute(builder: (context) => const CreateTicketScreen()),
              );
              if (result == true) {
                _refreshTickets();
              }
            },
            child: const Icon(Icons.add, color: Colors.white),
          ),
        );
      }
    );
  }

  Widget _buildTicketCard(BuildContext context, SupportTicket ticket) {
    Color statusColor;
    String statusText;

    switch (ticket.status) {
      case 'open':
        statusColor = Colors.amber;
        statusText = "Open";
        break;
      case 'replied':
        statusColor = Colors.blue;
        statusText = "Replied";
        break;
      case 'closed':
        statusColor = Colors.green;
        statusText = "Closed";
        break;
      default:
        statusColor = Colors.grey;
        statusText = ticket.status;
    }

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.05),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.white.withOpacity(0.1)),
      ),
      child: ListTile(
        contentPadding: const EdgeInsets.all(16),
        onTap: () async {
          await Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => TicketChatScreen(ticketId: ticket.id, subject: ticket.subject),
            ),
          );
          _refreshTickets();
        },
        title: Text(
          ticket.subject,
          style: GoogleFonts.poppins(
            color: Colors.white,
            fontWeight: FontWeight.w600,
            fontSize: 16,
          ),
        ),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const SizedBox(height: 8),
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                  decoration: BoxDecoration(
                    color: statusColor.withOpacity(0.2),
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(color: statusColor.withOpacity(0.5)),
                  ),
                  child: Text(
                    statusText,
                    style: GoogleFonts.poppins(
                      color: statusColor,
                      fontSize: 10,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
                const SizedBox(width: 8),
                Text(
                  _formatDate(ticket.updatedAt),
                  style: GoogleFonts.poppins(color: Colors.white38, fontSize: 12),
                ),
              ],
            ),
          ],
        ),
        trailing: const Icon(Icons.arrow_forward_ios, color: Colors.white24, size: 16),
      ),
    );
  }

  String _formatDate(DateTime date) {
    return "${date.day}/${date.month}/${date.year}";
  }
}
