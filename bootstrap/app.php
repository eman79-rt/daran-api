<?php

use App\Exceptions\ApiException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(api: __DIR__.'/../routes/api.php', commands: __DIR__.'/../routes/console.php', health: '/up')
    ->withMiddleware(function (Middleware $middleware): void {})
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            $status = match (true) {
                $e instanceof ValidationException => 422,
                $e instanceof AuthenticationException => 401,
                $e instanceof AuthorizationException => 403,
                $e instanceof ApiException => $e->status,
                $e instanceof HttpExceptionInterface => $e->getStatusCode(),
                default => 500,
            };

            $message = match (true) {
                $e instanceof ValidationException => 'البيانات المدخلة غير صالحة',
                $e instanceof AuthenticationException => 'يلزم تسجيل الدخول للوصول إلى هذا المورد',
                $e instanceof AuthorizationException, $status === 403 => 'غير مصرح لك بتنفيذ هذا الإجراء',
                $status === 404 => 'المورد غير موجود',
                $status >= 500 => 'حدث خطأ داخلي',
                default => $e->getMessage(),
            };

            $body = ['success' => false, 'error' => ['code' => $status, 'message' => $message]];
            if ($e instanceof ValidationException) {
                $body['error']['details'] = $e->errors();
            }

            return response()->json($body, $status);
        });
    })->create();
