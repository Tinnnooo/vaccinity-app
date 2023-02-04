<?php

namespace App\Models;

use App\Models\Regional;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Society extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'societies';

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

    public function regional()
    {
        return $this->belongsTo(Regional::class);
    }
}
