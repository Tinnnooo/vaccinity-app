<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spot_vaccine extends Model
{
    use HasFactory;

    protected $fillable = [
        'spot_id',
        'vaccine_id'
    ];

    public function vaccine(){
        return $this->belongsTo(Vaccine::class);
    }
}
