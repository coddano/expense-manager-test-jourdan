<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Expense;
use Illuminate\Http\Request;

class WebController extends Controller
{
    /**
     * Affiche le tableau de bord du Manager (voit tout)
     */
    public function managerDashboard()
    {
        $expenses = Expense::with('user:id,name') // Charge le nom de l'employé
                           ->orderBy('spent_at', 'desc')
                           ->get();

        return view('manager', ['expenses' => $expenses]);
    }

    /**
     * Affiche le tableau de bord d'un Employé (voit que les siennes)
     */
    public function employeeDashboard($id)
    {
        $employee = User::findOrFail($id);

        if ($employee->role === 'MANAGER') {
            abort(403, 'Pas un employé');
        }

        $expenses = $employee->expenses()
                             ->orderBy('spent_at', 'desc')
                             ->get();

        return view('employee', [
            'employee' => $employee,
            'expenses' => $expenses
        ]);
    }
}
