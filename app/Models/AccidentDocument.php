<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccidentDocument extends Model
{
    protected $fillable = [
        'accident_id',
        'document_type',
        'file_path',
        'description',
    ];

    // Relations
    public function accident(): BelongsTo
    {
        return $this->belongsTo(Accident::class);
    }

    // Accessors
    public function getDocumentTypeNameAttribute(): string
    {
        return match($this->document_type) {
            'constat' => 'Constat Amiable',
            'photo' => 'Photo',
            'rapport_police' => 'Rapport de Police',
            'expert' => 'Rapport d\'Expert',
            'medical' => 'Certificat Médical',
            'insurance' => 'Document Assurance',
            'repair' => 'Facture Réparation',
            'other' => 'Autre',
            default => ucfirst($this->document_type),
        };
    }
}
