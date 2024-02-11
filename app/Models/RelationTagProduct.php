<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelationTagProduct extends Model {
    use HasFactory;
    protected $table        = 'relation_tag_product';
    protected $fillable     = [
        'product_info_id',
        'tag_info_id'
    ];
    public $timestamps      = false;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new RelationTagProduct();
            foreach($params as $key => $value) $model->{$key}  = $value;
            $model->save();
            $id         = $model->id;
        }
        return $id;
    }

    public function infoTag() {
        return $this->hasOne(\App\Models\Tag::class, 'id', 'tag_info_id');
    }

    public function infoProduct() {
        return $this->hasOne(\App\Models\Product::class, 'id', 'product_info_id');
    }
}
