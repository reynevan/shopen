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

class TermsAndConditionsController
{
    public function index(): Response
    {
        return Inertia::render('Frontend/Static/TermsAndConditions');
    }
}