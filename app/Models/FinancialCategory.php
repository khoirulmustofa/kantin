<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialCategory extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'financial_categories';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $guarded = [];

    public function mutations()
    {
        return $this->hasMany(FinancialMutation::class);
    }
}
