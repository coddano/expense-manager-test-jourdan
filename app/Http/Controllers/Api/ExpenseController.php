<?php

namespace App\Http\Controllers\Api;

use App\Models\Export;
use App\Models\Expense;
use Illuminate\Http\Request;
use App\Jobs\ExportExpensesCsvJob;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\RejectExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ExpenseController extends Controller
{
    use AuthorizesRequests;
    /**
     * Afficher la liste des dépenses
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Expense::query()->with('user:id, name');

        if ($user->role ==='MANAGER') {
            //Le manager voit tout
        }else {
            //L'employé ne voit que ses dépenses
            $query->where('user_id', $user->id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->has('category')) {
            $query->where('category', $request->query('category'));
        }

        return response()->json(
            $query->orderBy('spent_at', 'desc')->get(),
        );
    }

    /**
     * Créer une nouvelle note de frais
     */
    public function store(StoreExpenseRequest $request)
    {
        $expense = Auth::user()->expenses()->create($request->validated());

        return response()->json($expense, 201);
    }


    /**
     * Mettre à jour une note de frais
     */
    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        $this->authorize('update', $expense);

        $expense->update($request->validated());

        return response()->json($expense);

    }

    /** */
    /**
     * L'employé soumet sa note ('DRAFT' -> 'SUBMITTED')
     */
    public function submit(Expense $expense)
    {
        $this->authorize('submit', $expense);

        $expense->status = 'SUBMITTED';
        $expense->save();

        return response()->json($expense);
    }

    /**
     * Manager approuve ('SUBMITTED' -> 'APPROVED')
     */

    public function approve(Expense $expense)
    {
        $this->authorize('manage', $expense);

        $expense->status = 'APPROVED';
        $expense->save();

        return response()->json($expense);
    }
    /**
     * Manager rejette ('SUBMITTED' -> 'REJECTED')
     */
    public function reject(RejectExpenseRequest $request, Expense $expense)
    {
        $this->authorize('manage', $expense);

        $expense->status = 'REJECTED';
        $expense->save();

        return response()->json($expense);
    }

    /**
     * Manager marque comme payé ('APPROVED' -> 'PAID')
     */

    public function pay(Expense $expense)
    {
        $this->authorize('manage', $expense);

        $expense->status = 'PAID';
        $expense->save();

        return response()->json($expense);
    }

    /**
     * Stats globales des notes de frais
     * Seuls les managers peuvent voir ça
     */
    public function statsSummary(Request $request)
    {

        $this->authorize('manage', Expense::class); //


        $request->validate(['period' => 'sometimes|date_format:Y-m']);
        $period = $request->query('period', Carbon::now()->format('Y-m'));


        $cacheKey = 'stats_summary_' . $period;


        $stats = Cache::remember($cacheKey, 60, function () use ($period) {


            [$year, $month] = explode('-', $period);

            return DB::table('expenses')
                ->whereYear('spent_at', $year)
                ->whereMonth('spent_at', $month)
                ->select(
                    'category',
                    DB::raw('COUNT(id) as total_count'),
                    DB::raw('SUM(amount) as total_amount')
                )
                ->groupBy('category')
                ->get();
        });


        return response()->json($stats);
    }

    /**
     * Lance une demande d'export CSV.
     */
    public function requestExport(Request $request)
    {
        $this->authorize('manage', Expense::class);


        $validated = $request->validate([
            'status' => 'sometimes|string|in:APPROVED,PAID,SUBMITTED',
            'period' => 'sometimes|date_format:Y-m',
        ]);


        $export = Auth::user()->exports()->create([
            'status' => 'PENDING',
            'meta' => $validated, // On stocke les filtres dans le JSON 'meta'
        ]);


        ExportExpensesCsvJob::dispatch($export);

        return response()->json($export, 202);
    }

    /**
     * Vérifie le statut d'un export et donne le lien si prêt.
     */
    public function getExportStatus(Export $export)
    {
        // Sécurité : seul le manager qui l'a demandé peut voir le statut
        if (Auth::id() !== $export->user_id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $data = [
            'id' => $export->id,
            'status' => $export->status,
            'file_url' => null,
        ];

        if ($export->status === 'READY') {
            // Génère un lien de téléchargement temporaire (valide 5 min)
            $data['file_url'] = Storage::temporaryUrl(
                $export->file_path,
                now()->addMinutes(5)
            );
        }

        return response()->json($data);
    }

}
