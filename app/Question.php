<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    //
    protected $fillable = ['content'];

    public $timestamps = false;

    public function naire()
    {
        return $this->belongsTo(Naire::class);
    }

    public function options()
    {
        return $this->hasMany(Option::class);
    }
}
