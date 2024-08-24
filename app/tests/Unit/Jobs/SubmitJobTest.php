<?php

namespace Tests\Unit\Jobs;

use App\Events\SubmissionSaved;
use App\Jobs\DTO\SubmitDTO;
use App\Jobs\SubmitJob;
use App\Listeners\LogSubmissionSavedListener;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class SubmitJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_dispatch_event_with_new_created_model(): void
    {
        // ARRANGE
        Event::fake();

        $data = [
            'name' => 'first name',
            'email' => fake()->unique()->safeEmail(),
            'message' => 'Lorem Ipsum',
        ];

        // ACT
        $job = new SubmitJob(new SubmitDTO(...$data));
        $job->handle();

        //ASSERT
        Event::assertListening(SubmissionSaved::class, LogSubmissionSavedListener::class);
        Event::assertDispatchedTimes(SubmissionSaved::class);
        Event::assertDispatched(
            SubmissionSaved::class,
            static function (SubmissionSaved $event) use ($data) {
                return $event->submission->name === $data['name']
                    && $event->submission->email === $data['email']
                    && $event->submission->message === $data['message']
                ;
            }
        );
    }
}
