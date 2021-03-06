<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller as BaseController;
use Dingo\Api\Routing\Helpers;

/**
 * Class PingController.
 *
 * @author Jose Fonseca <jose@ditecnologia.com>
 */
class PingController extends BaseController
{
   use Helpers;

   /**
    * Responds with a status for heath check.
    *
    * @return \Illuminate\Http\JsonResponse
    */
   public function index()
   {
      return $this->response->array([
         'status' => 'ok',
         'timestamp' => \Carbon\Carbon::now(),
      ]);
   }
}
