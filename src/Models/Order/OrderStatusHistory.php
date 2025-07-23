<?php

namespace Shopen\Models\Order;

use Illuminate\Database\Eloquent\Model;
use Shopen\Enums\Order\OrderStatus;

class OrderStatusHistory extends Model
{

    protected $fillable = [
        'status',
        'comment',
        'email_notification',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'email_notification' => 'bool'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getTimeFormattedAttribute()
    {
        return $this->created_at->translatedFormat('M d, Y H:i:s');
    }
}
