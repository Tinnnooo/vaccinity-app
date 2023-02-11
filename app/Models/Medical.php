<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medical extends Model
{
    use HasFactory;

    protected $fillable = [
        'spot_id',
        'user_id',
        'role',
        'name'
    ];

    public function users(){
        return $this->hasMany(User::class);
    }

    public function spot(){
        return $this->belongsTo(Spot::class);
    }

    
    public function vaccinations(){
        return $this->hasMany(Vaccination::class);
    }
}
