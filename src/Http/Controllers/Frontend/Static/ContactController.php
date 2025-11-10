<?php

namespace Shopen\Http\Controllers\Frontend\Static;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Requests\Frontend\Contact\StoreContactMessageRequest;
use Shopen\Mail\ContactMessage\ContactMessage as ContactMessageMail;
use Shopen\Models\ContactMessage\ContactMessage;
use Shopen\Models\User;

class ContactController
{
    public function index(): Response
    {
        return Inertia::render('Frontend/Static/Contact/Index', [
            'turnstile_site_key' => config('services.turnstile.key')
        ]);
    }

    public function submit(StoreContactMessageRequest $request)
    {
        $data = $request->validated();
        $message = new ContactMessage($data);
        $message->user_id = Auth::id();
        $message->save();

        $admins = User::query()->admins()->get();

        Mail::to($admins)->queue(new ContactMessageMail($message));

        return back()->with('success', 'Wiadomość została wysłana.');
    }
}