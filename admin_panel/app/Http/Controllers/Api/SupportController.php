<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupportController extends Controller
{
    // List all tickets for the authenticated user
    public function index(Request $request)
    {
        $tickets = SupportTicket::where('user_id', $request->user()->id)
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Tickets retrieved successfully',
            'data' => $tickets
        ]);
    }

    // Create a new ticket
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'in:low,medium,high',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $ticket = SupportTicket::create([
            'user_id' => $request->user()->id,
            'subject' => $request->subject,
            'status' => 'open',
            'priority' => $request->priority ?? 'low',
        ]);

        // Create initial message
        SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'sender_id' => $request->user()->id,
            'sender_type' => 'user',
            'message' => $request->message,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Ticket created successfully',
            'data' => $ticket
        ], 201);
    }

    // Get ticket details with messages
    public function show(Request $request, $id)
    {
        $ticket = SupportTicket::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with('messages')
            ->first();

        if (!$ticket) {
            return response()->json([
                'status' => false,
                'message' => 'Ticket not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Ticket details retrieved successfully',
            'data' => $ticket
        ]);
    }

    // Reply to a ticket
    public function reply(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $ticket = SupportTicket::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$ticket) {
            return response()->json([
                'status' => false,
                'message' => 'Ticket not found'
            ], 404);
        }

        // Create message
        $message = SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'sender_id' => $request->user()->id,
            'sender_type' => 'user',
            'message' => $request->message,
        ]);

        // Update ticket status to open if it was closed (optional logic)
        // or keep it as is. Usually user reply re-opens ticket if closed?
        // Let's keep it simple for now, maybe update timestamp.
        $ticket->touch();

        return response()->json([
            'status' => true,
            'message' => 'Reply sent successfully',
            'data' => $message
        ]);
    }
}
