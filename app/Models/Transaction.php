<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'amount',
        'currency',
        'description',
        'type',
        'posted_by',
        'date_posted'
    ];


    /**
     * Format dates
     * @var string[]
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public $timestamps = false;
}
    public function user()
    {
        return $this->belongsTo(User::class, 'posted_by', 'id');
    }
