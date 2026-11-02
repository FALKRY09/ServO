<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function index(Request $request, Ticket $ticket)
    {
        $this->authorizeTicket($request, $ticket);
        return TicketAttachment::where('ticket_id', $ticket->id)->latest()->paginate(20);
    }

    public function store(Request $request, Ticket $ticket)
    {
        $this->authorizeTicket($request, $ticket);
        $request->validate(['file' => ['required', 'file', 'max:10240', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,txt,zip']]);
        $file = $request->file('file');
        $path = $file->store('ticket-attachments', 'local');

        return response()->json(TicketAttachment::create([
            'ticket_id' => $ticket->id,
            'uploaded_by_id' => $request->user()->id,
            'original_name' => $file->getClientOriginalName(),
            'stored_name' => basename($path),
            'path' => $path,
            'disk' => 'local',
            'mime_type' => $file->getMimeType(),
            'size_bytes' => $file->getSize(),
        ]), 201);
    }

    public function download(Request $request, TicketAttachment $attachment)
    {
        $this->authorizeTicket($request, $attachment->ticket);
        abort_unless(Storage::disk($attachment->disk)->exists($attachment->path), 404);
        return Storage::disk($attachment->disk)->download($attachment->path, $attachment->original_name);
    }

    private function authorizeTicket(Request $request, Ticket $ticket): void
    {
        $user = $request->user();
        $assigned = $ticket->assignments()->where('assignee_id', $user->id)->whereNull('unassigned_at')->exists();
        abort_unless(in_array($user->role?->name, ['Admin', 'Koordinator'], true) || $ticket->requester_id === $user->id || $assigned, 403);
    }
}
