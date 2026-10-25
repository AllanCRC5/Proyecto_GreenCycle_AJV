<?php

return [
    // Límite de árboles activos por usuario.
    'max_active_trees' => (int) env('GREENCYCLE_MAX_ACTIVE_TREES', 5),

    // Reglas de crecimiento.
    'max_level' => (int) env('GREENCYCLE_MAX_LEVEL', 10),
    'health_initial' => 100,
    'health_max' => 100,
    'health_per_care' => (int) env('GREENCYCLE_CARE_HEAL', 20),
    'progress_initial' => 0,

    // Cooldown del cuidado manual.
    'care_cooldown_minutes' => (int) env('GREENCYCLE_CARE_COOLDOWN_MINUTES', 60),

    // Reglas de deterioro automático.
    'decay_interval_minutes' => (int) env('GREENCYCLE_DECAY_INTERVAL_MINUTES', 1440),
    'decay_amount' => (int) env('GREENCYCLE_DECAY_AMOUNT', 20),
];
