<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExpensePolicy
{
    /**
     * Un manager peut tout voir, un employee peut voir ses propres depenses
     */
    public function view(User $user, Expense $expense): bool
    {
        if ($user->role === 'MANAGER') {
            return true;
        }

        return $user->id === $expense->user_id;
    }

    /**
     * Seuls les employés peuvent créer des depenses
     */

    public function create(User $user): bool
    {
        return $user->role === 'EMPLOYEE';
    }

    /**
     * Un employé ne peut modifier que SES propres brouillons ('DRAFT')
     */

    public function update(User $user, Expense $expense): bool
    {
        return $user->id === $expense->user_id && $expense->status === 'DRAFT';
    }

    /**
     * Un employé ne peut soumettre que SES propres brouillons ('DRAFT')
     */

    public function submit(User $user, Expense $expense): bool
    {
        return $user->id === $expense->user_id && $expense->status === 'DRAFT';
    }

    /**
     * Seul le manager peut gérer les depenses
     */

    public function manage(User $user): bool
    {
        return $user->role === 'MANAGER';
    }
}
