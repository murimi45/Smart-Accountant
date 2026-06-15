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
use RuntimeException;
use Throwable;

class RunPromotion implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 1;
    public int $timeout = 300;
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
        public string $type
    ) {}

    public function handle(PromotionService $service): void
    {
        $this->assertPromotionRun();

        if (! $this->claimPromotionRun()) {
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
            $fromTerm = Term::findForSchoolOrFail($this->schoolId, $this->fromTermId);
            $toTerm   = Term::findForSchoolOrFail($this->schoolId, $this->toTermId);

            $service->promoteToNextClass(
                $this->promotionRunId,
                $this->schoolId,
                $fromTerm,
                $toTerm,
                $this->userId
            );
        }
    }

    public function failed(Throwable $exception): void
    {
        PromotionRun::withoutGlobalScopes()
            ->where('id', $this->promotionRunId)
            ->where('school_id', $this->schoolId)
            ->update([
                'status'        => 'failed',
                'error_message' => $exception->getMessage(),
                'active_key'    => null,
            ]);
    }

    private function assertPromotionRun(): PromotionRun
    {
        $run = PromotionRun::findForSchoolOrFail($this->schoolId, $this->promotionRunId);

        if (
            (int) $run->from_term_id !== $this->fromTermId
            || (int) $run->to_term_id !== $this->toTermId
        ) {
            throw new RuntimeException('Promotion run payload does not match the queued job.');
        }

        return $run;
    }

    private function claimPromotionRun(): bool
    {
        $run = PromotionRun::findForSchool($this->schoolId, $this->promotionRunId);

        if (! $run || $run->status === 'completed') {
            return false;
        }

        if ($run->status === 'running') {
            return true;
        }

        return (bool) PromotionRun::withoutGlobalScopes()
            ->where('id', $this->promotionRunId)
            ->where('school_id', $this->schoolId)
            ->whereIn('status', ['pending', 'failed'])
            ->update(['status' => 'running']);
    }
}
