<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AccountType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$accountTypes): Response
    {
   // Get the current authenticated user
   $user = Auth::user();

   // Check if the user account_type matches any of the specified roles
   if (in_array($user->account_type, $accountTypes)) {
       return $next($request);
   }

   // If the user does not have the required account_type, return 403 Forbidden
   return abort(403, 'Forbidden');
   //return response()->json(['message' => 'Forbidden'], 403);

    }
}
