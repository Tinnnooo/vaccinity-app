<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vaccination extends Model
{
    use HasFactory;

    protected $fillable = [
        'dose',
        'date',
        'society_id',
        'spot_id',
        'vaccine_id',
        'doctor_id',
        'officer_id'
    ];

    public function society(){
        return $this->belongsTo(Society::class);
    }

    public function spot(){
        return $this->belongsTo(Spot::class);
    }

    public function vaccine(){
        return $this->belongsTo(Vaccine::class);
    }

    public function medical(){
        return $this->belongsTo(Medical::class);
    }
}
