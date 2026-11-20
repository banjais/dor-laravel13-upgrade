<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
        'urls',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof PostTooLargeException
            && $request->is('admin/chatbot-knowledge/upload')) {

            $message = 'File is too large (maximum 300 MB).';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 413);
            }

            return redirect()->route('admin.chatbot-knowledge.index', [
                'upload_error' => 'too_large',
            ]);
        }

        return parent::render($request, $e);
    }

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
