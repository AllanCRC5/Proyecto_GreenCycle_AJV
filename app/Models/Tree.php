<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tree extends Model
{
    use HasFactory;

    /* Lo único que el cliente puede influir al plantar. Nivel, salud, estado y fechas nunca se asignan desde el request.*/
    protected $fillable = [
        'user_id',
        'seed_type',
    ];

    protected function casts(): array
    {
        return [
            'seed_type' => SeedType::class,
            'status' => TreeStatus::class,
            'last_care_at' => 'datetime',
            'next_care_at' => 'datetime',
            'last_decay_at' => 'datetime',
            'harvested_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Sprint 3: el modelo Effect todavía no existe.
    public function effects(): HasMany
    {
        return $this->hasMany(Effect::class);
    }

    // ---------------------------------------------------------------
    // Reglas del juego
    // ---------------------------------------------------------------

    /*
      Cuántos cuidados necesita este árbol para subir un nivel.
      semilla básica = 5, semilla especial = 3.
      También se usa en el Resource como "progress_required".
     */
    public function careRequired(): int
    {
        return $this->seed_type === 'SPECIAL' ? 3 : 5;
    }

    /*
      ¿El árbol sigue en cooldown? (todavía no puede recibir otro cuidado)
      next_care_at es null si nunca ha sido cuidado, y en ese caso no hay cooldown.
     */
    public function isInCooldown(): bool
    {
        return $this->next_care_at !== null && $this->next_care_at->isFuture();
    }

    /*
      Aplica el cuidado.
      El controlador debe revisar antes que el árbol esté ACTIVE y sin cooldown,
      y responder 409 si no se cumple. Aquí solo se modifican los datos.
     */
    public function applyCare(): void
    {
        // El cuidado suma 1 de progreso y recupera 20 de salud (máximo 100)
        $this->progress++;
        $this->health = min(100, $this->health + config('greencycle.care_heal', 20));

        // Si llegó a los cuidados requeridos, sube de nivel y el progreso vuelve a 0
        if ($this->progress >= $this->careRequired()) {
            $this->level++;
            $this->progress = 0;
        }

        // Nivel máximo (10): el árbol queda listo para cosechar
        if ($this->level >= config('greencycle.max_level', 10)) {
            $this->status = 'MATURE';
        }

        // Guardamos cuándo se cuidó y cuándo podrá cuidarse otra vez
        $this->last_care_at = now();
        $this->next_care_at = now()->addMinutes(config('greencycle.care_cooldown_minutes', 60));

        $this->save();
    }

    /*
      Aplica el deterioro por tiempo. Se puede llamar muchas veces seguidas sin aplicar dos veces el mismo intervalo (es "idempotente").
      Lo llaman dos lugares:
       - el comando programado (para todos los árboles)
       - el controlador de cuidado (justo antes de cuidar, por si ya debería estar muerto)
     */
    public function applyDecay(): void
    {
        // Solo se deterioran los árboles ACTIVE
        if ($this->status !== 'ACTIVE') {
            return;
        }

        $interval = config('greencycle.decay_interval_minutes', 1440); // 24 h
        $amount   = config('greencycle.decay_amount', 20);

        // 1. Punto de referencia: el momento más reciente entre el último cuidado, el último deterioro procesado y la creación.
        $reference = $this->created_at;

        if ($this->last_care_at !== null && $this->last_care_at->gt($reference)) {
            $reference = $this->last_care_at;
        }
        if ($this->last_decay_at !== null && $this->last_decay_at->gt($reference)) {
            $reference = $this->last_decay_at;
        }

        // 2. Cuántos intervalos completos pasaron desde la referencia
        $intervals = intdiv((int) $reference->diffInMinutes(now()), $interval);

        // Si no pasó ni un intervalo completo, no hay nada que hacer
        if ($intervals < 1) {
            return;
        }

        // 3. Bajar la salud, sin pasar de 0
        $this->health = max(0, $this->health - ($amount * $intervals));

        // 4. Guardar hasta dónde se procesó. Se usa referencia + intervalos (NO now()) para no perder el tiempo sobrante que aún no completa otro intervalo.
        $this->last_decay_at = $reference->copy()->addMinutes($intervals * $interval);

        // 5. Sin salud, el árbol muere
        if ($this->health === 0) {
            $this->status = 'DEAD';
        }

        $this->save();
    }
}