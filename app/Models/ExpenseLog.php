<?php

namespace App\Models;

use App\Models\User;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExpenseLog extends Model
{
    use HasFactory;

    /**
     * Permet de désactiver les timestamps
     */
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'expense_id',
        'from_status',
        'to_status',
        'created_at',
    ];

    /**
     * Permet d'obtenir la dépense liée à ce log
     */

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    /**
     * Permet d'obtenir l'utilisateur qui fait l'action
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
