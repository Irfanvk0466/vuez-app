<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Requests\messageRequest;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Display a list of all bidders for the admin to choose from.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $bidders = User::where('role', 'bidder')->get();
        return view('chat.users', compact('bidders'));
    }
    /**
     * Display the chat conversation between the admin and a specific bidder.
     *
     * @param int $bidderId
     * @return \Illuminate\View\View
     */
    public function show($bidderId)
    {
        $bidder = User::findOrFail($bidderId);
        $messages = Chat::with('sender')
            ->whereIn('sender_id', [Auth::id(), $bidder->id])
            ->whereIn('receiver_id', [Auth::id(), $bidder->id])
            ->oldest()
            ->get();
        return view('chat.index', compact('messages', 'bidder', 'bidderId'));
    }
    /**
     * Display the chat conversation between the logged-in bidder and the admin.
     *
     * @return \Illuminate\View\View
     */
    public function showAdminMessage()
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $messages = Chat::with('sender')
            ->whereIn('sender_id', [Auth::id(), $admin->id])
            ->whereIn('receiver_id', [Auth::id(), $admin->id])
            ->oldest()
            ->get();
        return view('chat.index', [
            'messages' => $messages,
            'adminId' => $admin->id
        ]);
    }
    /**
     * Send a chat message and broadcast it via Pusher.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(messageRequest $request)
    {
        $chat = Chat::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);
        $chat->load('sender');
        event(new MessageSent($chat));
        return response()->json(['status' => 'success']);
    }
}
