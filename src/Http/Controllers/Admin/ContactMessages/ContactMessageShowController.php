<?php

namespace Shopen\Http\Controllers\Admin\ContactMessages;

use Inertia\Inertia;
use Inertia\Response;
use Shopen\Enums\ContactMessage\Status;
use Shopen\Http\Resources\Admin\ContactMessage\ContactMessageResource;
use Shopen\Models\ContactMessage\ContactMessage;

class ContactMessageShowController
{

    public function show(ContactMessage $contactMessage): Response
    {
        $contactMessage->load(['responses.user']);

        if ($contactMessage->status !== Status::READ) {
            $contactMessage->status = Status::READ;
            $contactMessage->save();
        }

        return Inertia::render('Admin/ContactMessage/Show', [
            'message' => ContactMessageResource::make($contactMessage)
        ]);
    }
}