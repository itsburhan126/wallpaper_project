class SupportMessage {
  final int id;
  final int senderId;
  final String senderType;
  final String message;
  final DateTime createdAt;

  SupportMessage({
    required this.id,
    required this.senderId,
    required this.senderType,
    required this.message,
    required this.createdAt,
  });

  factory SupportMessage.fromJson(Map<String, dynamic> json) {
    return SupportMessage(
      id: json['id'],
      senderId: json['sender_id'],
      senderType: json['sender_type'],
      message: json['message'],
      createdAt: DateTime.parse(json['created_at']),
    );
  }
}

class SupportTicket {
  final int id;
  final String subject;
  final String status;
  final String priority;
  final DateTime updatedAt;
  final DateTime createdAt;
  final List<SupportMessage> messages;

  SupportTicket({
    required this.id,
    required this.subject,
    required this.status,
    required this.priority,
    required this.updatedAt,
    required this.createdAt,
    this.messages = const [],
  });

  factory SupportTicket.fromJson(Map<String, dynamic> json) {
    var messagesList = <SupportMessage>[];
    if (json['messages'] != null) {
      messagesList = (json['messages'] as List)
          .map((m) => SupportMessage.fromJson(m))
          .toList();
    }

    return SupportTicket(
      id: json['id'],
      subject: json['subject'],
      status: json['status'],
      priority: json['priority'],
      updatedAt: DateTime.parse(json['updated_at']),
      createdAt: DateTime.parse(json['created_at']),
      messages: messagesList,
    );
  }
}
