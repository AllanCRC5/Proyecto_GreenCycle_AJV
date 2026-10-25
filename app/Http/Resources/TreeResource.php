<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TreeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'seed_type' => $this->seed_type,
            'level' => $this->level,
            'health' => $this->health,
            'progress' => $this->progress,
            'progress_required' => $this->seed_type->careRequiredPerLevel(),
            'status' => $this->status,
            'last_care_at' => $this->last_care_at,
            'next_care_at' => $this->next_care_at,
            'last_decay_at' => $this->last_decay_at,
            'created_at' => $this->created_at,
        ];
    }
}
