<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model {
    use HasFactory;
    protected $table        = 'product_price';
    protected $fillable     = [
        'product_info_id',
        'name', 
        'description',
        'price',
        'price_origin',
        'sale_off',
        'instock',
        'folder_drive'
    ];
    public $timestamps = false;
    private static $columnFilter = [
        'product_info_id',
        'name', 
        'description',
        'price',
        'price_origin',
        'price_before_promotion',
        'sale_off',
        'instock',
        'folder_drive'
    ];

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new ProductPrice();
            foreach($params as $key => $value) {
                if(in_array($key, self::$columnFilter)) $model->{$key}  = $value;
            }
            $model->save();
            $id         = $model->id;
        }
        return $id;
    }

    public static function updateItem($id, $params){
        $flag           = false;
        if(!empty($id)&&!empty($params)){
            $model      = self::find($id);
            foreach($params as $key => $value) {
                if(in_array($key, self::$columnFilter)) $model->{$key}  = $value;
            }
            $flag       = $model->update();
        }
        return $flag;
    }

    public function files(){
        return $this->hasMany(\App\Models\SystemFile::class, 'attachment_id', 'id')->where('relation_table', 'product_price');
    }

    public function sources(){
        return $this->hasMany(\App\Models\SourceFile::class, 'attachment_id', 'id');
    }
}
