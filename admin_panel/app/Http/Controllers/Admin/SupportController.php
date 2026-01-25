<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => SupportTicket::count(),
            'open' => SupportTicket::where('status', 'open')->count(),
            'replied' => SupportTicket::where('status', 'replied')->count(),
            'closed' => SupportTicket::where('status', 'closed')->count(),
        ];
        $tickets = SupportTicket::with('user')->orderBy('updated_at', 'desc')->paginate(10);
        return view('admin.support.index', compact('tickets', 'stats'));
    }

    public function show($id)
    {
        $ticket = SupportTicket::with(['user', 'messages'])->findOrFail($id);
        return view('admin.support.show', compact('ticket'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = SupportTicket::findOrFail($id);

        SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'sender_id' => auth()->id(),
            'sender_type' => 'admin',
            'message' => $request->message,
        ]);

        $ticket->status = 'replied';
        $ticket->save();

        return back()->with('success', 'Reply sent successfully.');
    }

    public function destroy($id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $ticket->delete();

        return back()->with('success', 'Ticket deleted successfully.');
    }
}
