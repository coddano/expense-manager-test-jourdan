<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ExpenseController; // Ajoute ça

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

//Route de Login (publique)
Route::post('/login', function (Request $request){
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|'
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidateException::withMessages([
            'email' => ['Ces identifiants ne sont pas corrects.'],
        ]);
    }
    // Supprimer les tokens existants et créer un nouveau token
    $user->tokens()->delete();
    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'user' => $user,
        'token' => $token,
    ]);
});


// Routes protégées
Route::middleware('auth:sanctum')->group(function () {

    // Obtenir l'utilisateur connecté
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // ----- DÉPENSES (EXPENSES) -----
    // GET /api/expenses (Liste filtrée)
    Route::get('/expenses', [ExpenseController::class, 'index']);

    // POST /api/expenses (Création)
    Route::post('/expenses', [ExpenseController::class, 'store']);

    // PUT /api/expenses/{id} (Modification)
    Route::put('/expenses/{expense}', [ExpenseController::class, 'update']);

    // POST /api/expenses/{id}/submit (Soumission employé)
    Route::post('/expenses/{expense}/submit', [ExpenseController::class, 'submit']);

    // POST /api/expenses/{id}/approve (Approbation manager)
    Route::post('/expenses/{expense}/approve', [ExpenseController::class, 'approve']);

    // POST /api/expenses/{id}/reject (Rejet manager)
    Route::post('/expenses/{expense}/reject', [ExpenseController::class, 'reject']);

    // POST /api/expenses/{id}/pay (Paiement manager)
    Route::post('/expenses/{expense}/pay', [ExpenseController::class, 'pay']);

    // ----- STATS & EXPORTS -----
    // GET /api/stats/summary?period=YYYY-MM
    Route::get('/stats/summary', [ExpenseController::class, 'statsSummary']);

    // POST /api/exports/expenses?status=APPROVED&period=YYYY-MM
    Route::post('/exports/expenses', [ExpenseController::class, 'requestExport']);

    // GET /api/exports/{id} (Récupération du lien)
    Route::get('/exports/{export}', [ExpenseController::class, 'getExportStatus']);
});
