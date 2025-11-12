<?php

namespace App\Jobs;

use App\Models\Export;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Exception;
use App\Models\Expense;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExportExpensesCsvJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct( protected Export $export)
    {
        //
    }

    /**
     * Exécute le job.
     */
    public function handle(): void
    {
        try {
            // 1. Récupère les filtres stockés dans le champ 'meta'
            $filters = $this->export->meta;
            $period = $filters['period'] ?? null;
            $status = $filters['status'] ?? 'APPROVED'; // Par défaut 'APPROVED'

            // 2. Construit la requête
            $query = Expense::query()->where('status', $status);
            if ($period) {
                [$year, $month] = explode('-', $period);
                $query->whereYear('spent_at', $year)->whereMonth('spent_at', $month);
            }
            $expenses = $query->with('user:id,name')->get();

            // 3. Crée le nom du fichier et le chemin
            $filename = 'export_' . $this->export->id . '_' . time() . '.csv';
            $path = 'exports/' . $filename; // Sera dans storage/app/exports/

            // 4. Ouvre un "pointeur" vers le fichier temporaire
            $tempFile = tmpfile();
            $f = fputcsv($tempFile, ['ID', 'Title', 'Employee', 'Amount', 'Category', 'Date']);

            // 5. Remplit le CSV
            foreach ($expenses as $expense) {
                fputcsv($tempFile, [
                    $expense->id,
                    $expense->title,
                    $expense->user->name, // grâce au 'with()'
                    $expense->amount,
                    $expense->category,
                    $expense->spent_at->format('Y-m-d'),
                ]);
            }

            // 6. Sauvegarde le fichier de la mémoire vers le disque
            rewind($tempFile); // Remet le pointeur au début
            Storage::put($path, stream_get_contents($tempFile));
            fclose($tempFile);

            // 7. Met à jour l'entrée 'Export' dans la BDD : C'EST PRÊT !
            $this->export->status = 'READY';
            $this->export->file_path = $path;
            $this->export->save();

        } catch (Exception $e) {
            // 7b. En cas d'erreur : C'EST RATÉ !
            $this->export->status = 'FAILED';
            $this->export->save();

        }
    }
}
