<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'description', 'price', 'stock_quantity'];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeGetAllProdcuts($query) {
        return $query->select('products.*');
    }
}
