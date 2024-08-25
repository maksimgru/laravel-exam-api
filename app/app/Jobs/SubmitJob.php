<?php

namespace App\Jobs;

use App\Events\SubmissionSaved;
use App\Jobs\DTO\SubmitDTO;
use App\Models\Submission;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use function event;

class SubmitJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly SubmitDTO $submitDTO,
    ) {
    }

    public function handle(): void
    {
        $submission = Submission::create([
            'name' => $this->submitDTO->name,
            'email' => $this->submitDTO->email,
            'message' => $this->submitDTO->message,
        ]);

        // We have several variant to dispatch domain event after model has been saved:
        // (1) (current) Event should implement interface "ShouldDispatchAfterCommit" and listener - "ShouldHandleEventsAfterCommit"
        // (2) In configs/queue.php we can set option ['after_commit' => true] for selected queue connection
        // (3) Using Closures in protected static function boot() { static::created(function ($model) {...}) }
        // (4) Using Observers: Create an observer for model and register this observer in the boot method on AppServiceProvider
        event(new SubmissionSaved($submission));
    }
}
