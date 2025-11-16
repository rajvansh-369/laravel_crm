<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactCustomField extends Model
{
    protected $guarded = [];

    public function contact(){

        return $this->belongsTo(Contact::class);
    }
}
