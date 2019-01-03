<?php

namespace App\Providers;

use Laravel\Passport\Bridge\PersonalAccessGrant;
use Laravel\Passport\Passport;
use Laravel\Passport\PassportServiceProvider;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;

/**
 * Class V4TPassportServiceProvider
 * <p>
 * Extends PassportServiceProvider to fix the token expiration for PersonalAccessGrant
 * </p>
 * @package App\Providers
 */
class V4TPassportServiceProvider extends PassportServiceProvider
{
   /**
    * Bootstrap the application services.
    *
    * @return void
    */
   public function boot()
   {

      parent::boot();
   }


   /**
    * Register the service provider.
    *
    * @return void
    */
   public function register()
   {
      parent::register();
   }

   /**
    * Register the authorization server.
    *
    * @return void
    */
   protected function registerAuthorizationServer()
   {
      $this->app->singleton(AuthorizationServer::class, function () {
         return tap($this->makeAuthorizationServer(), function ($server) {
            $server->enableGrantType(
               $this->makeAuthCodeGrant(), Passport::tokensExpireIn()
            );

            $server->enableGrantType(
               $this->makeRefreshTokenGrant(), Passport::tokensExpireIn()
            );

            $server->enableGrantType(
               $this->makePasswordGrant(), Passport::tokensExpireIn()
            );

            $server->enableGrantType(
//               new PersonalAccessGrant, new DateInterval('P1M')
               new PersonalAccessGrant, Passport::tokensExpireIn()
            );

            $server->enableGrantType(
               new ClientCredentialsGrant, Passport::tokensExpireIn()
            );

            if (Passport::$implicitGrantEnabled) {
               $server->enableGrantType(
                  $this->makeImplicitGrant(), Passport::tokensExpireIn()
               );
            }
         });
      });
   }
}
