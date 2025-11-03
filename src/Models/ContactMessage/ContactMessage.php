<?php

namespace Shopen\Models\ContactMessage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Shopen\Enums\ContactMessage\Status;

class ContactMessage extends Model
{
    protected $casts = [
        'status' => Status::class,
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
    ];

    public function responses(): HasMany
    {
        return $this->hasMany(ContactMessageResponse::class);
    }
}
