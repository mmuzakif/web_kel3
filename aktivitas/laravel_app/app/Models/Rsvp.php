<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rsvp extends Model
{
    protected $table = 'rsvp';

    public $timestamps = false;

    protected $guarded = [];

    public function acara()
    {
        return $this->belongsTo(Acara::class, 'acara_id');
    }
}
