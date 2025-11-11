<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Expense;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExpenseWorkflowTest extends TestCase
{
    use RefreshDatabase;


    public function test_employee_cannot_see_others_expenses(): void
    {
        // 1. Crée deux employés
        $employeeA = User::factory()->create(['role' => 'EMPLOYEE']);
        $employeeB = User::factory()->create(['role' => 'EMPLOYEE']);

        // 2. Crée une dépense pour l'employé A
        $expenseA = Expense::factory()->create(['user_id' => $employeeA->id]);

        // 3. Connecte-toi en tant qu'employé B (via API Sanctum)
        Sanctum::actingAs($employeeB);

        // 4. Essaie d'accéder à la liste des dépenses
        $response = $this->getJson('/api/expenses');

        // 5. Vérifie que la dépense de A n'est PAS dans la réponse
        $response->assertStatus(200)
                 ->assertJsonMissing(['id' => $expenseA->id]);
    }

    public function test_employee_cannot_approve_expense(): void
    {
        // 1. Crée un employé et une de ses dépenses
        $employee = User::factory()->create(['role' => 'EMPLOYEE']);
        $expense = Expense::factory()->create([
            'user_id' => $employee->id,
            'status' => 'SUBMITTED' // Elle doit être soumise
        ]);

        // 2. Connecte-toi en tant que cet employé
        Sanctum::actingAs($employee);

        // 3. Essaie d'approuver sa propre dépense
        $response = $this->postJson('/api/expenses/' . $expense->id . '/approve');

        // 4. Vérifie que c'est "Interdit" (403 Forbidden)
        $response->assertStatus(403);
    }

    public function test_manager_can_approve_expense(): void
    {
        // 1. Crée un manager
    $manager = User::factory()->create(['role' => 'MANAGER']);

    // 2. CRÉE UN EMPLOYÉ D'ABORD
    $employee = User::factory()->create(['role' => 'EMPLOYEE']);

    // 3. Crée une dépense QUI APPARTIENT À L'EMPLOYÉ
    $expense = Expense::factory()->create([
        'user_id' => $employee->id,  // <-- C'EST LA CORRECTION
        'status' => 'SUBMITTED'
    ]);

    // 4. Connecte-toi en tant que manager
    Sanctum::actingAs($manager);

    // 5. Approuve la dépense
    $response = $this->postJson('/api/expenses/' . $expense->id . '/approve');
    

        // 6. Bonus : Vérifie que le statut a VRAIMENT changé dans la BDD
        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'status' => 'APPROVED'
        ]);

        // 7. Bonus : Vérifie que le LOG a été créé par l'Observer !
        $this->assertDatabaseHas('expense_logs', [
            'expense_id' => $expense->id,
            'from_status' => 'SUBMITTED',
            'to_status' => 'APPROVED'
        ]);
    }

}
