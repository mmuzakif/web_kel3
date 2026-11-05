<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Acara extends Model
{
    protected $table = 'acara';

    public $timestamps = false;

    protected $guarded = [];

    public function rsvps()
    {
        return $this->hasMany(Rsvp::class, 'acara_id');
    }
}
