<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invited extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'diagram_id'];

    //Relacion uno a muchos con diagram
    public function diagram(){
        return $this->belongsTo(Diagram::class, 'diagram_id');
    }

    //Relacion uno a muchos con user
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
