<?php

namespace App\Http\Middleware;

use Closure;

class OnlyGoodRoute
{
   /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request $request
    * @param  \Closure $next
    * @return mixed
    */
   public function handle($request, Closure $next)
   {
//      Log::debug("OnlyRoute ============================================================");
//      Log::debug($request->fullUrl());
//      Log::debug($request->getScriptName());
//      Log::debug($request->getRequestUri());
      if (strncmp('/index.php', $request->getRequestUri(), 10) == 0) {
         header('Location: ' . $request->getSchemeAndHttpHost() . '/', true, 302);
         die();
      }
      return $next($request);
   }
}
