<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobAutoTranslateLinks extends Model {
    use HasFactory;
    protected $table        = 'job_auto_translate_links';
    protected $fillable     = [
        'seo_id',
        'ordering',
        'language',
        'link_source',
        'link_translate'
    ];
    public $timestamps = false;

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new JobAutoTranslateLinks();
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

    
}
