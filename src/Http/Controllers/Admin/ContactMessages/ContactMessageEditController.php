<?php

namespace Shopen\Http\Controllers\Admin\ContactMessages;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Shopen\Enums\ContactMessage\Status;
use Shopen\Http\Requests\Admin\ContactMessage\RespondContactMessageRequest;
use Shopen\Mail\ContactMessage\ContactMessageResponse as ContactMessageResponseMail;
use Shopen\Models\ContactMessage\ContactMessage;
use Shopen\Models\ContactMessage\ContactMessageResponse;
use Throwable;

class ContactMessageEditController
{

    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->delete();
        return back();
    }

    public function respond(RespondContactMessageRequest $request, ContactMessage $contactMessage)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $response = new ContactMessageResponse();
            $response->contact_message_id = $contactMessage->id;
            $response->message = $data['message'];
            $response->user_id = Auth::id();
            $response->save();

            $contactMessage->status = Status::REPLIED;
            $contactMessage->save();

            Mail::to($contactMessage->email)->queue(new ContactMessageResponseMail($contactMessage, $response));

            DB::commit();

        } catch (Throwable $e) {
            Log::error($e->getMessage());
            DB::rollBack();
        }
        return back()->with('success', 'Wiadomość została wysłana');
    }
}