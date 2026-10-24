<?php

namespace App\Enums;

enum TreeStatus: string
{
    case ACTIVE = 'ACTIVE';
    case MATURE = 'MATURE';
    case DEAD = 'DEAD';
    case HARVESTED = 'HARVESTED';
}
