<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use App\Notifications\ContactMessageReceived;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    public function store(StoreContactMessageRequest $request): JsonResponse
    {
        $contactMessage = ContactMessage::create([
            ...$request->safe()->only(['name', 'email', 'message']),
            'ip' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 250, ''),
        ]);

        if ($adminEmail = config('portfolio.admin_email')) {
            Notification::route('mail', $adminEmail)->notify(new ContactMessageReceived($contactMessage));
        }

        return response()->json(['message' => 'Thanks! Your message has been sent.'], 201);
    }
}
