<?php

namespace App\Enums;

enum SeedType: string
{
    case BASIC = 'BASIC';
    case SPECIAL = 'SPECIAL';

    public function careRequiredPerLevel(): int
    {
        return match ($this) {
            self::BASIC => 5,
            self::SPECIAL => 3,
        };
    }

    public function harvestReward(): int
    {
        return match ($this) {
            self::BASIC => 10,
            self::SPECIAL => 7,
        };
    }
}
