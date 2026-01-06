<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $user = $request->user();

            // Check if the user's status is approved
            if ($user->status !== 'approved') {
                if ($user->status === 'pending') {
                    return response()->json([
                        'message' => 'لم يتم قبول طلبك بعد. الرجاء الانتظار حتى موافقة الإدارة.'
                    ], 403);
                } elseif ($user->status === 'rejected') {
                    return response()->json([
                        'message' => 'تم رفض طلبك. لا يمكن الوصول إلى هذه الميزة.'
                    ], 403);
                }
            }
        }

        return $next($request);
    }
}
