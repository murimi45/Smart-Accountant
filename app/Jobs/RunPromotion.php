<?php

namespace App\Jobs;

use App\Models\Term;
use App\Models\PromotionRun;
use App\Services\PromotionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class RunPromotion implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /*
    |--------------------------------------------------------------------------
    | Job retries — do NOT retry promotion jobs automatically.
    | A failed promotion must be reviewed by a human before retrying
    | to avoid duplicate enrollments.
    |--------------------------------------------------------------------------
    */
    public int $tries   = 1;
    public int $timeout = 300; // 5 minutes — enough for large schools

    public int $uniqueFor = 3600;

    public function uniqueId(): string
    {
        return "{$this->schoolId}-{$this->fromTermId}-{$this->toTermId}-{$this->type}";
    }

    public function __construct(
        public int    $promotionRunId,
        public int    $schoolId,
        public int    $fromTermId,
        public int    $toTermId,
        public int    $userId,
        public string $type  // 'term' | 'class'
    ) {}

    public function handle(PromotionService $service): void
    {
        if (!$this->claimPromotionRun()) {
            return;
        }

        if ($this->type === 'term') {

            $service->promoteToNextTerm(
                $this->promotionRunId,
                $this->schoolId,
                $this->fromTermId,
                $this->toTermId,
                $this->userId
            );

        } else {

            $fromTerm = Term::findOrFail($this->fromTermId);
            $toTerm   = Term::findOrFail($this->toTermId);

            $service->promoteToNextClass(
                $this->promotionRunId,
                $this->schoolId,
                $fromTerm,
                $toTerm,
                $this->userId
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | FAILED — marks the PromotionRun as failed with the error message
    |--------------------------------------------------------------------------
    */
    public function failed(Throwable $exception): void
    {
        PromotionRun::where('id', $this->promotionRunId)->update([
            'status'        => 'failed',
            'error_message' => $exception->getMessage(),
            'active_key'    => null,
        ]);
    }

    /**
     * Claim the run (pending/failed → running) or resume if already running.
     * Skip if already completed.
     */
    private function claimPromotionRun(): bool
    {
        $run = PromotionRun::find($this->promotionRunId);

        if (!$run || $run->status === 'completed') {
            return false;
        }

        if ($run->status === 'running') {
            return true;
        }

        return (bool) PromotionRun::where('id', $this->promotionRunId)
            ->whereIn('status', ['pending', 'failed'])
            ->update(['status' => 'running']);
    }
}