<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [ ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [ 'date' ];

    /**
     * Related stock
     */
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    /**
     * Related user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}