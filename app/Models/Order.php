<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{

    use HasUuids, SoftDeletes;

    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function mutation()
    {
        return $this->morphOne(FinancialMutation::class, 'reference');
    }
}
