<?php

namespace Shopen\Http\Controllers\Admin\ContactMessages;

use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Resources\Admin\ContactMessage\ContactMessageResource;
use Shopen\Repositories\ContactMessage\ContactMessageRepository;

class ContactMessageIndexController
{
    public function __construct(
        protected ContactMessageRepository $contactMessageRepository
    )
    {
    }

    public function index(): Response
    {
        $messages = $this->contactMessageRepository->getPaginated(request('sort', 'id'), request('dir', 'desc'), request('q'));

        return Inertia::render('Admin/ContactMessage/Index', [
            'messages' => ContactMessageResource::collection($messages),
            'sort' => request('sort', 'id'),
            'dir' => request('dir', 'desc'),
            'q' => request('q')
        ]);
    }
}