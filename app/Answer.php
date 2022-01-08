<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    //
    protected $fillable = ['value'];

    public $timestamps = false;

    public function option()
    {
        return $this->belongsTo(Option::class);
    }
}
