<?php

namespace App\Listeners;

use App\Events\SubmissionSaved;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Psr\Log\LoggerInterface;
use Throwable;

class LogSubmissionSavedListener implements ShouldQueue, ShouldHandleEventsAfterCommit
{
    use InteractsWithQueue;

    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function handle(SubmissionSaved $event): void
    {
        $context = [
            'name' =>  $event->submission->name,
            'email' => $event->submission->email,
        ];

        $this->logger->info(
            message: 'Submission Saved with: ',
            context: $context,
        );
    }

    public function failed(SubmissionSaved $event, Throwable $exception): void
    {
        $context = [
            'name' =>  $event->submission->name,
            'email' => $event->submission->email,
        ];

        $this->logger->error(
            message: 'Fail Submission Save with: ' . $exception->getMessage(),
            context: $context,
        );
    }
}
