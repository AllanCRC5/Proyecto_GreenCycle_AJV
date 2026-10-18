<?php

return [
    // Límite de árboles activos por usuario.
    'max_active_trees' => (int) env('GREENCYCLE_MAX_ACTIVE_TREES', 5),

    // Reglas de crecimiento.
    'max_level' => 10,
    'health_initial' => 100,
    'health_max' => 100,
    'health_per_care' => 20,
    'progress_initial' => 0,

    // Cooldown del cuidado manual.
    'care_cooldown_minutes' => 60,

    // Reglas de deterioro automático.
    'decay_interval_minutes' => 1440,
    'decay_amount' => 20,
];
