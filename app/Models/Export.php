<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Export extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'status', 'file_path', 'meta'];

    protected $casts = [
        'meta' => 'json' , // Important
    ];

    /**
     * Permet d'obtenir l'utilisateur qui a demandé l'export.
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
