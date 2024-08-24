<?php

namespace Tests\Feature\Http\Controllers;

use App\Jobs\DTO\SubmitDTO;
use App\Jobs\SubmitJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\ReflectionTrait;
use Tests\TestCase;

class SubmissionControllerTest extends TestCase
{
    use RefreshDatabase;
    use ReflectionTrait;

    public function test_ok_api_submit_endpoint(): void
    {
        // ARRANGE
        Queue::fake();
        $payload = [
            'name' => 'first name',
            'email' => 'example@mail.com',
            'message' => 'Lorem Ipsum',
        ];

        // ACT
        $response = $this->post('/api/submit', $payload);

        // ASSERT
        $response->assertStatus(200);
    }

    public function test_invalid_request(): void
    {
        // ARRANGE
        Queue::fake();
        $payload = [];

        // ACT
        $response = $this->post('/api/submit', $payload);
        $data = $response->getContent();
        $data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

        // ASSERT
        $response->assertStatus(422);
        $this->assertCount(3, $data['fields']);
        $this->assertEquals('The name field is required.', $data['fields']['name'][0]);
        $this->assertEquals('The email field is required.', $data['fields']['email'][0]);
        $this->assertEquals('The message field is required.', $data['fields']['message'][0]);
    }

    public function test_should_dispatch_SubmitJob(): void
    {
        // ARRANGE
        Queue::fake();
        $payload = [
            'name' => 'first name',
            'email' => 'example@mail.com',
            'message' => 'Lorem Ipsum',
        ];

        // ACT
        $response = $this->post('/api/submit', $payload);

        // ASSERT
        $response->assertStatus(200);

        Queue::assertPushed(
            SubmitJob::class,
            function (SubmitJob $job) use ($payload) {
                $submitDTO = $this->getNonPublicValue($job, 'submitDTO');
                $expectedSubmitDTO = new SubmitDTO(...$payload);

                $this->assertEquals(
                    $expectedSubmitDTO,
                    $submitDTO
                );

                return true;
            }
        );

    }
}
