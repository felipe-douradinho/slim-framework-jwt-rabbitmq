<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{

    /**
     * Related quotes
     */
    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

}