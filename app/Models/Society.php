<?php

namespace App\Models;

use App\Models\Regional;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class Society extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'societies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_card_number',
        'password',
        'name',
        'born-date',
        'gender',
        'address',
        'regional_id',
        'login_tokens',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function username()
{
    return 'id_card_number';
}

    public function regional()
    {
        return $this->belongsTo(Regional::class);
    }

    public function vaccinations(){
        return $this->hasMany(Vaccination::class);
    }
}
