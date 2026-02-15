<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TailorUniFormUser extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'tailor_uni_form_users';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function uniform()
    {
        return $this->belongsTo(Uniform::class);
    }

    public function tailorMeasurement()
    {
        return $this->belongsTo(TailorMeasurement::class);
    }
}
