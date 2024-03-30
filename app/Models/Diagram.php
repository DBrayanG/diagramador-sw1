<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagram extends Model
{
    use HasFactory;

    protected $fillable = ['titulo', 'descripcion', 'contenido', 'user_id'];

    //Relacion uno a muchos con user
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    //Relacion uno 
    public function inviteds(){
        return $this->hasMany(Invited::class);
    }

}
