<?php

namespace App\Models;

use App\Models\Society;
use Illuminate\Database\Eloquent\Model;

class Regional extends Model
{
    protected $table = 'regionals';

    protected $fillable = [
        'province', 'district'
    ];

    public function societies()
    {
        return $this->hasMany(Society::class);
    }
}
