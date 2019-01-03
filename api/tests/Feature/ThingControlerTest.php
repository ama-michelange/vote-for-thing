<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ThingControlerTest extends TestCase
{
   use RefreshDatabase;

   protected function setUp()
   {
      parent::setUp();
      //      Log::debug('>>> Tests\ThingTest->setUp : ' . get_class($this));
      $this->artisan('db:migrate', ['--from' => 'json']);
      //      $this->artisan('db:migrate', ['--from' => 'json', '--quiet' => true]);
   }
   /**
    * A basic test example.
    *
    * @return void
    */
   public function testBasicTest()
   {
      $response = $this->json('GET', 'api/things');
      $response->assertStatus(200);
   }
}
