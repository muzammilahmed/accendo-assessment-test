<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateCSV
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // is file exist in request payload
        if (!$request->has('organizationFile')) {
            return response(['message' => __('failureMessage.file_not_exist')], 403);
        }
        // validate file extension
        if ($request->file('organizationFile')->extension() !== config('app.file_type')) {
            return response(['message' => __('failureMessage.file_type')], 403);
        }
        // check file size
        if ($request->file('organizationFile')->getSize() > config('app.file_size')) {
            return response(['message' => __('failureMessage.file_size')], 403);
        }

        return $next($request);
    }
}
