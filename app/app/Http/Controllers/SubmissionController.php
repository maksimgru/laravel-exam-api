<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitRequest;
use App\Jobs\DTO\SubmitDTO;
use App\Jobs\SubmitJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class SubmissionController extends Controller
{
    public function __invoke(SubmitRequest $request): JsonResponse
    {
        SubmitJob::dispatch(new SubmitDTO(
            name: $request->name,
            email: $request->email,
            message: $request->message,
        ));

        return response()->json('OK', JsonResponse::HTTP_OK);
    }
}
