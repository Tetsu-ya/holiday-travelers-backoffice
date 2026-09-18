<?php

namespace App\Console\Commands;

use App\Services\AIResourcePlanningService;
use Illuminate\Console\Command;

class RunAutomaticResourcePlanning extends Command
{
    protected $signature = 'ai:resource-plan';

    protected $description = 'Generate automated demand, supplier, marketing, and capacity planning recommendations';

    public function handle(AIResourcePlanningService $planner): int
    {
        $plans = $planner->runAutomaticPlanning();

        if (! $this->output->isQuiet()) {
            $this->info("Automatic resource planning completed: {$plans->count()} plans generated.");
            $this->table(
                ['Type', 'Subject', 'Confidence'],
                $plans->map(fn ($plan) => [$plan->plan_type, $plan->subject ?: '—', $plan->confidence_score . '%'])->all()
            );
        }

        return self::SUCCESS;
    }
}
