<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Naire extends Model
{
    //

    protected $fillable = ['content'];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
