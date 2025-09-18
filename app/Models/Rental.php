<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rental extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'book_id',
        'rented_at',
        'due_at',
        'returned_at',
        'fine_amount',
    ];
    protected $casts = [
        'rented_at'   => 'datetime',
        'due_at'      => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book() : BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
