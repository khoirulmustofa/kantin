<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'purchase_orders';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $guarded = [];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function mutation()
    {
        return $this->morphOne(FinancialMutation::class, 'reference');
    }

    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class);
    }
}
