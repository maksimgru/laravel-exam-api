<?php

namespace App\Jobs;

use App\Events\SubmissionSaved;
use App\Jobs\DTO\SubmitDTO;
use App\Models\Submission;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use function event;

class SubmitJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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

        event(new SubmissionSaved($submission));
    }
}
