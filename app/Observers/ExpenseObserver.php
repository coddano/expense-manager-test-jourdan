<?php

namespace App\Observers;

use App\Models\Expense;
use App\Models\ExpenseLog;
use Illuminate\Support\Facades\Auth;

class ExpenseObserver
{
    /**
     * Handle the Expense "created" event.
     */
    public function created(Expense $expense): void
    {
        //
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        // Vérifie si c'est le champ 'status' qui a changé
        // isDirty() retourne true si le champ a été modifié avant de sauver l'enregistrement
        if ($expense->isDirty('status')) {

            ExpenseLog::create([
                'expense_id' => $expense->id,
                'user_id' =>Auth::id(),
                'from_status' => $expense->getOriginal('status'),
                'to_status' => $expense->status,
                'created_at' => now(),
            ]);
        }
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        //
    }

    /**
     * Handle the Expense "restored" event.
     */
    public function restored(Expense $expense): void
    {
        //
    }

    /**
     * Handle the Expense "force deleted" event.
     */
    public function forceDeleted(Expense $expense): void
    {
        //
    }
}
