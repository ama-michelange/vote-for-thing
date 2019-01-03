<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
   /**
    * The policy mappings for the application.
    *
    * @var array
    */
   protected $policies = [
      'App\Model' => 'App\Policies\ModelPolicy'
   ];

   /**
    * Register any authentication / authorization services.
    *
    * @return void
    */
   public function boot()
   {
      $this->registerPolicies();

      Passport::tokensCan([
         'vft-vote' => 'Vote for things',
         'vft-coach-group' => 'Coach and manage a vote group',
         'vft-admin' => 'Manage the application',
      ]);
      Passport::enableImplicitGrant();
      //
      Passport::routes();
      Passport::tokensExpireIn(Carbon::now()->addHour(2));
//      Passport::tokensExpireIn(Carbon::now()->addHour(1));
//      Passport::tokensExpireIn(Carbon::now()->addMinute(5));

   }
}
