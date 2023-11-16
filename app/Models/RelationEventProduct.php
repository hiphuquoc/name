<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelationEventProduct extends Model {
    use HasFactory;
    protected $table        = 'relation_event_product';
    protected $fillable     = [
        'product_info_id',
        'event_info_id'
    ];
    public $timestamps      = false;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new RelationEventProduct();
            foreach($params as $key => $value) $model->{$key}  = $value;
            $model->save();
            $id         = $model->id;
        }
        return $id;
    }

    public function infoEvent() {
        return $this->hasOne(\App\Models\Event::class, 'id', 'event_info_id');
    }

    public function infoProduct() {
        return $this->hasOne(\App\Models\Product::class, 'id', 'product_info_id');
    }
}
