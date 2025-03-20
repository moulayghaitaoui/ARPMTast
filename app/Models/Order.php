<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{


    use HasFactory;

    // تحديد الأعمدة القابلة للتعبئة
    protected $fillable = [
        'customer_id',
        'status',
        'completed_at',
    ];


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
