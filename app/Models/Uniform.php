<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Uniform extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'uniforms';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    protected $guarded = [];

    public function tailorUniFormUsers()
    {
        return $this->hasMany(TailorUniFormUser::class);
    }
}
