<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $guarded = [];


    public function custom_field(){

        return $this->hasMany(ContactCustomField::class);
    }
    
    public function mergedPhones(){

        return $this->hasMany(ContactPhone::class);
    }
   
    public function mergedEmails(){

        return $this->hasMany(ContactEmail::class);
    }
}
