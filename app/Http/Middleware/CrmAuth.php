<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class CrmAuth {
    public function handle(Request $request, Closure $next) {
        if (!session('crm_authenticated')) {
            return redirect()->route('crm.access');
        }
        return $next($request);
    }
}
