<?php

namespace App\Console\Commands;

use App\Enums\TreeStatus;
use App\Models\Tree;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ApplyTreeDecay extends Command
{
    protected $signature = 'trees:apply-decay';

    protected $description = 'Aplica el deterioro automático a los árboles activos';

    public function handle(): int
    {
        $processed = 0;

        Tree::query()
            ->where('status', TreeStatus::ACTIVE->value)
            ->chunkById(100, function ($trees) use (&$processed) {
                foreach ($trees as $tree) {
                    DB::transaction(function () use ($tree, &$processed) {
                        $lockedTree = Tree::query()
                            ->whereKey($tree->id)
                            ->lockForUpdate()
                            ->first();

                        if (
                            $lockedTree === null
                            || $lockedTree->status !== TreeStatus::ACTIVE
                        ) {
                            return;
                        }

                        $lockedTree->applyDecay();

                        $processed++;
                    });
                }
            });

        $this->info("Árboles revisados: {$processed}");

        return self::SUCCESS;
    }
}
