<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price'
    ];


//    public function orderItems(): \Illuminate\Database\Eloquent\Relations\BelongsTo
//    {
////        return $this->belongsTo(Orderitem::class); //product is belongs to order items.
//        return $this->belongsTo(Orderitem::class,"product_id","id"); //product is belongs to order items.
////        return $this->belongsTo(Department::class, 'department_id', "id");
//
//    }
}
//'order',
