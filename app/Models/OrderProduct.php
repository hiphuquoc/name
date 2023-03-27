<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model {
    use HasFactory;
    protected $table        = 'order_product';
    protected $fillable     = [
        'order_info_id',
        'product_info_id', 
        'product_price_id',
        'quantity',
        'price'
    ];
    public $timestamps = false;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new OrderProduct();
            foreach($params as $key => $value) $model->{$key}  = $value;
            $model->save();
            $id         = $model->id;
        }
        return $id;
    }

    public static function updateItem($id, $params){
        $flag           = false;
        if(!empty($id)&&!empty($params)){
            $model      = self::find($id);
            foreach($params as $key => $value) $model->{$key}  = $value;
            $flag       = $model->update();
        }
        return $flag;
    }

    public function infoProduct() {
        return $this->hasOne(\App\Models\Product::class, 'id', 'product_info_id');
    }

    public function infoPrice() {
        return $this->hasOne(\App\Models\ProductPrice::class, 'id', 'product_price_id');
    }
}
