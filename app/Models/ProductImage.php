<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductImage extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'product_images';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
