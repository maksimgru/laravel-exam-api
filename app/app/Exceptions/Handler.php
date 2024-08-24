<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    public function register(): void
    {
        $this->renderable(function (NotFoundHttpException $e) {
            // Custom message used because it renders namespace of not found model.
            return $this->renderHttpException($e, 'Data not found.');
        });

        $this->renderable(function (HttpExceptionInterface $e) {
            return $this->renderHttpException($e);
        });

        $this->renderable(function (ValidationException $e): void {
            throw new UnprocessableEntityHttpException($e->getMessage(), $e);
        });

        $this->renderable(function (Throwable $e) {
            return $this->convertExceptionToResponse($e);
        });
    }

    protected function renderHttpException(
        HttpExceptionInterface $e,
        string $message = null,
    ): JsonResponse {
        return response()->json(
            $this->constructHttpExceptionPayload($e, $message),
            $e->getStatusCode(),
            $e->getHeaders()
        );
    }

    protected function convertExceptionToResponse(Throwable $e): JsonResponse
    {
        $headers = method_exists($e, 'getHeaders')
            ? $e->getHeaders()
            : []
        ;

        $statusCode = method_exists($e, 'getStatusCode')
            ? $e->getStatusCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR
        ;

        $title = method_exists($e, 'getTitle')
            ? $e->getTitle()
            : 'HTTP Internal Server Error'
        ;

        $payload = [
            'title'   => $title,
            'message' => $e->getMessage(),
            'code'    => $e->getCode(),
        ];

        return response()->json($payload, $statusCode, $headers);
    }

    private function constructHttpExceptionPayload(
        HttpExceptionInterface $e,
        string $message = null,
    ): array {
        $fields = [];
        $code = $e->getCode();
        $message = $message ?? $e->getMessage();
        $statusCode = $e->getStatusCode();
        $statusTexts = Response::$statusTexts;
        $title = 'HTTP ' . Arr::get($statusTexts, $statusCode, 'Error Occurred');

        if (isset($e->getPrevious()->validator)) {
            $fields = $e->getPrevious()
                ->validator
                ->errors()
                ->getMessages()
            ;
        }

        return compact('title', 'message', 'code', 'fields');
    }
}
