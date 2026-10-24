<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TreeResource extends JsonResource
{
    /**
     * Qué campos del árbol se le muestran al front.
     * Funciona igual que tu UserResource: $this es el Tree.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'seed_type'         => $this->seed_type,
            'level'             => $this->level,
            'health'            => $this->health,
            'progress'          => $this->progress,
            'progress_required' => $this->careRequired(), // el método que dejamos en el modelo
            'status'            => $this->status,
            'last_care_at'      => $this->last_care_at?->toISOString(),
            'next_care_at'      => $this->next_care_at?->toISOString(),
            'created_at'        => $this->created_at->toISOString(),
        ];
    }
}