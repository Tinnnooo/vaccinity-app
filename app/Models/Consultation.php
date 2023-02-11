<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'society_id',
        'doctor_id',
        'status',
        'disease_history',
        'current_symtomps',
        'doctor_notes'
    ];

    public function society(){
        return $this->belongsTo(Society::class);
    }

    public function medical(){
        return $this->belongsTo(Medical::class);
    }
}
