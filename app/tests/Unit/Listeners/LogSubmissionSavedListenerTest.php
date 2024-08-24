<?php

namespace Tests\Unit\Listeners;

use App\Events\SubmissionSaved;
use App\Listeners\LogSubmissionSavedListener;
use App\Models\Submission;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

class LogSubmissionSavedListenerTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_log_info(): void
    {
        // ARRANGE
        Queue::fake();
        Event::fake();

        $data = [
            'name' => 'first name',
            'email' => fake()->unique()->safeEmail(),
            'message' => 'Lorem Ipsum',
        ];

        $submission = Submission::factory()->create($data);
        $event = new SubmissionSaved($submission);

        $loggerMock = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $loggerMock
            ->expects($this->once())
            ->method('info')
            ->with(
                'Submission Saved with: ',
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                ]
            )
        ;

        // ACT
        $listener = new LogSubmissionSavedListener($loggerMock);
        $listener->handle($event);
    }

    public function test_should_log_error(): void
    {
        // ARRANGE
        Queue::fake();
        Event::fake();

        $data = [
            'name' => 'first name',
            'email' => fake()->unique()->safeEmail(),
            'message' => 'Lorem Ipsum',
        ];

        $submission = Submission::factory()->create($data);
        $event = new SubmissionSaved($submission);

        $loggerMock = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $loggerMock
            ->expects($this->once())
            ->method('error')
            ->with(
                'Fail Submission Save with: ',
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                ]
            )
        ;

        // ACT
        $listener = new LogSubmissionSavedListener($loggerMock);
        $listener->failed($event, new Exception());
    }
}
