<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialAccount extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'financial_accounts';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $guarded = [];

    public function mutations()
    {
        return $this->hasMany(FinancialMutation::class);
    }
}
