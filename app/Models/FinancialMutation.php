<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialMutation extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'financial_mutations';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $guarded = [];

    public function account()
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    // Relasi ke Kategori
    public function category()
    {
        return $this->belongsTo(FinancialCategory::class, 'financial_category_id');
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
