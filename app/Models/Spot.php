<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spot extends Model
{
    use HasFactory;

    protected $fillable = [
        'regional_id',
        'name',
        'address',
        'serve',
        'capacity'
    ];

    public function medicals(){
        return $this->hasMany(Medical::class);
    }

    public function regional(){
        return $this->belongsTo(Regional::class);
    }

    public function vaccinations(){
        return $this->hasMany(Vaccination::class);
    }
}
