<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [ ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [ 'password' ];

    /**
     * Related quotes
     */
    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

}