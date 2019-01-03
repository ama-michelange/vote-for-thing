<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class DatabaseMigrateTestCase extends TestCase
{
   use RefreshDatabase;

   protected function setUp()
   {
      parent::setUp();
//      $this->artisan('db:migrate', ['--from' => 'json']);
      $this->artisan('db:migrate', ['--from' => 'json', '--quiet' => true]);
   }
}
