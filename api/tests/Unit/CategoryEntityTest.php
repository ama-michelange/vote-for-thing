<?php

namespace Tests\Unit;

use Domain\Entity\CategoryEntity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryEntityTest extends TestCase
{
   use RefreshDatabase;

   protected function setUp()
   {
      parent::setUp();
//      $this->artisan('db:migrate', ['--from' => 'json']);
      $this->artisan('db:migrate', ['--from' => 'json', '--quiet' => true]);
   }

   /**
    * @test
    */
   public function study_Find_with_relation()
   {
      $cat = CategoryEntity::find(1);
      $this->assertNotNull($cat);
//      Log::debug('>>>>>>>>>>>>>>>>>>>>>>>>>>> ' . 'CategoryEntity::find(1)');
//      Log::debug(print_r($cat->toArray(), true));

      $thing = $cat->things()->first();
      $this->assertNotNull($thing);
//      Log::debug('>>>>>>>>>>>>>>>>>>>>>>>>>>> ' . 'cat->things()->first()');
//      Log::debug(print_r($thing->toArray(), true));

      $things = $cat->things()->limit(5)->get();
      $this->assertNotNull($things);
      $this->assertEquals(5, $things->count());
//      Log::debug('>>>>>>>>>>>>>>>>>>>>>>>>>>> ' . 'cat->things()->limit(10)->get()');
//      Log::debug(print_r($things->toArray(), true));

      $things = $cat->things()->limit(7)->orderBy('lib_title', 'desc')->get();
      $this->assertNotNull($things);
      $this->assertEquals(7, $things->count());
//      Log::debug('>>>>>>>>>>>>>>>>>>>>>>>>>>> ' . 'cat->things()->limit(5)->orderBy(\'lib_title\',\'desc\')->get()');
//      Log::debug(print_r($things->toArray(), true));
   }

   /**
    * @group current
    * @test
    */
   public function given_CategoryEntity_when_new_instance()
   {
      $thing = new CategoryEntity();
      $this->assertCount(0, $thing->attributesToArray());
      $this->assertCount(1, $thing->getFillable());
      $this->assertCount(0, $thing->getHidden());
      $this->assertCount(4, $thing->getVisible());

      $fillable = [
         'name',
      ];
      $this->assertEquals($fillable, $thing->getFillable());

      $hidden = [];
      $this->assertEquals($hidden, $thing->getHidden());

      $visible = [
         'name',
         'id',
         'created_at',
         'updated_at',
      ];
      $this->assertEquals($visible, $thing->getVisible());

   }
}
