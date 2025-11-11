<?php

namespace App\Models;

use App\Models\User;
use App\Models\ExpenseLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'amount',
        'currency',
        'spent_at',
        'category',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2', // Important pour l'argent
        'spent_at' => 'date', // Important pour les dates
    ];

    /**
     * Permet d'obtenir le propriétaire de la dépense
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Permet d'obtenir l'historique de la dépense
     */

    public function logs(): HasMany
    {
        return $this->hasMany(ExpenseLog::class);
    }
}
