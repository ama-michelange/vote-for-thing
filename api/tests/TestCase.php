<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

abstract class TestCase extends BaseTestCase
{
   use CreatesApplication;
//, DatabaseMigrations;

//   protected function setUp()
//   {
//      parent::setUp();
//      Log::debug('>>> Tests\TestCase->setUp : ' . get_class($this));
////        Artisan::call('db:seed', [ '--class' => 'UsersTableSeeder' ]);
////      Artisan::call('db:seed');
////      Log::debug('<<< Tests\TestCase->setUp : ' . get_class($this));
//   }
}
