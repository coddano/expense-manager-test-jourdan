<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebController;

Route::get('/', function () {
    return view('welcome');
});

// Route pour la vue Manager
Route::get('/manager', [WebController::class, 'managerDashboard'])->name('manager.dashboard');

// Route pour la vue Employé (on passe l'ID de l'employé en URL)
// Par exemple : /employee/2 pour voir 'alice@demo.com'
Route::get('/employee/{id}', [WebController::class, 'employeeDashboard'])->name('employee.dashboard');

// Route d'accueil
Route::get('/', function () {
    // On redirige vers le tableau manager par défaut
    // (utilise les ID de tes seeders !)
    return 'Allez sur /manager ou /employee/2 ou /employee/3';
});
