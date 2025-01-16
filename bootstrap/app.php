<?php

use App\Traits\ResponseTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/status',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->use([
            TrustProxies::class,
            HandleCors::class,
            PreventRequestsDuringMaintenance::class,
            ValidatePostSize::class,
            TrimStrings::class,
            ConvertEmptyStringsToNull::class,
        ]);

        $middleware->alias([
            'auth' => Authenticate::class,
            'guest' => RedirectIfAuthenticated::class,
            'precognitive' => HandlePrecognitiveRequests::class,
            'signed' => ValidateSignature::class,
            'throttle' => ThrottleRequests::class,
            'verified' => EnsureEmailIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, $request) {
            if ($request->expectsJson()) {
                $handler = new class
                {
                    use ResponseTrait;
                };

                $statusCode = 500;
                $message = __('fail.unexpected_error');
                $data = null;

                if ($e instanceof OAuthServerException) {
                    $statusCode = $e->getHttpStatusCode();
                    $message = $e->getHint();
                } elseif ($e instanceof NotFoundHttpException) {
                    $statusCode = Response::HTTP_NOT_FOUND;
                    $message = $e->getMessage() ?? __('fail.route_not_found');
                } elseif ($e instanceof AccessDeniedHttpException) {
                    $statusCode = Response::HTTP_FORBIDDEN;
                    $message = __('auth.unauthorized');
                } elseif ($e instanceof HttpException) {
                    $statusCode = $e->getStatusCode();
                    $message = $e->getMessage();
                } elseif ($e instanceof AuthenticationException) {
                    $statusCode = Response::HTTP_UNAUTHORIZED;
                    $message = __('auth.unauthenticated');
                } elseif ($e instanceof ValidationException) {
                    $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
                    $message = __('fail.validation_error');
                    $data = $e->validator->errors()->toArray();
                }

                if ($statusCode >= 500) {
                    Log::critical($e->getMessage(), ['exception' => $e]);
                    if (config('app.debug') === true) {
                        return false;
                    }
                    // Here you could add notification logic (e.g., Slack, email)
                }

                return $handler->error($message, $statusCode, $data);
            }

            // For non-JSON requests, use Laravel default exception handling
            return false;
        });

        // Register custom exception report callback
        $exceptions->report(function (Throwable $e) {
            if (app()->bound('sentry') && $this->shouldReport($e)) {
                app('sentry')->captureException($e);
            }
        });
    })->create();