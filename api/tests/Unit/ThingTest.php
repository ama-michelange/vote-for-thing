<?php

namespace Tests\Unit;

use App\Entity\Category;
use App\Entity\Thing;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ThingTest extends TestCase
{
   //   use DatabaseMigrations;
   use RefreshDatabase;

   protected function setUp()
   {
      parent::setUp();
      //      Log::debug('>>> Tests\ThingTest->setUp : ' . get_class($this));
      $this->artisan('db:migrate', ['--from' => 'json']);
      //      $this->artisan('db:migrate', ['--from' => 'json', '--quiet' => true]);
   }

   /**
    * Clean up the testing environment before the next test.
    *
    * @return void
    */
   protected function tearDown()
   {
      //      Log::debug('<<< Tests\ThingTest->setUp : ' . get_class($this));
      parent::tearDown();
   }

   public function testDifferenceBetweenFirstOrGet()
   {
      $this->assertNotNull(Thing::where('lib_title', 'Batchalo')->first());
      $this->assertEquals(Thing::class, get_class(Thing::where('lib_title', 'Batchalo')->first()));
      $this->assertEquals('Batchalo', Thing::where('lib_title', 'Batchalo')->first()->title);

      $this->assertNotNull(Thing::where('lib_title', 'Batchalo')->get());
      $this->assertCount(1, Thing::where('lib_title', 'Batchalo')->get());
      $this->assertEquals(Collection::class, get_class(Thing::where('lib_title', 'Batchalo')->get()));

      $this->assertNotNull('Batchalo', Thing::where('lib_title', 'Batchalo')->get()[0]);
      $this->assertEquals(Thing::class, get_class(Thing::where('lib_title', 'Batchalo')->get()[0]));
      $this->assertEquals('Batchalo', Thing::where('lib_title', 'Batchalo')->get()[0]->title);

      $this->assertNotNull(
         'Batchalo',
         Thing::where('lib_title', 'Batchalo')
            ->get()
            ->get(0)
      );
      $this->assertEquals(
         Thing::class,
         get_class(
            Thing::where('lib_title', 'Batchalo')
               ->get()
               ->get(0)
         )
      );
      $this->assertEquals(
         'Batchalo',
         Thing::where('lib_title', 'Batchalo')
            ->get()
            ->get(0)->title
      );
   }

   public function testDifferenceBetweenFirstOrGetWithOneToMany()
   {
      $thing = Thing::where('lib_title', 'Batchalo')->first();

      $this->assertNotNull($thing);
      $this->assertNotNull($thing->category);
      $this->assertEquals('comic', $thing->category->name);

      $this->assertNotNull($thing->category());
      $this->assertEquals(BelongsTo::class, get_class($thing->category()));

      $this->assertNotNull($thing->category()->get());
      $this->assertEquals(Collection::class, get_class($thing->category()->get()));
      $this->assertCount(1, $thing->category()->get());

      $this->assertEquals(Category::class, get_class($thing->category()->get()[0]));
      $this->assertEquals('comic', $thing->category()->get()[0]->name);

      $this->assertEquals(
         Category::class,
         get_class(
            $thing
               ->category()
               ->get()
               ->get(0)
         )
      );
      $this->assertEquals(
         'comic',
         $thing
            ->category()
            ->get()
            ->get(0)->name
      );

      $this->assertEquals(Category::class, get_class($thing->category()->first()));
      $this->assertNotNull($thing->category()->first());
      $this->assertEquals('comic', $thing->category()->first()->name);
   }

   private function traceThingsWhere($pThings, $pWhere)
   {
      $cpt = 0;
      Log::debug(sprintf('%s : %s', str_repeat('=', 20), $pWhere));
      foreach ($pThings as $value) {
         $cpt++;
         Log::debug(sprintf('%d) %s : %s - %s', $cpt, $value->lib_title, $value->title, $value->number));
      }
   }

   public function testWhere()
   {
      $things = Thing::where('lib_title', 'Batchalo')->get();
      $this->traceThingsWhere($things, "Thing::where('lib_title', 'Batchalo')->get()");
      $this->assertCount(1, $things);

      $things = Thing::where('lib_title', 'like', 'B%')->get();
      $this->traceThingsWhere($things, "Thing::where('lib_title', 'like', 'B%')->get()");
      $this->assertNotEmpty($things);

      $things = Thing::where('lib_title', 'like', 'B%')
         ->orderBy('lib_title')
         ->get();
      $this->traceThingsWhere($things, "Thing::where('lib_title', 'like', 'B%')->orderBy('lib_title')->get()");
      $this->assertNotEmpty($things);

      $things = Thing::where('lib_title', 'like', 'a%')
         ->orderBy('lib_title')
         ->orderBy('number', 'desc')
         ->get();
      $this->traceThingsWhere($things, "Thing::where('lib_title', 'like', 'a%')->orderBy('lib_title')->orderBy('number', 'desc')->get()");
      $this->assertNotEmpty($things);

      $things = Thing::where('lib_title', 'like', '%b%')
         ->orderBy('lib_title')
         ->orderBy('number', 'asc')
         ->get();
      $this->traceThingsWhere($things, "Thing::where('lib_title', 'like', '%b%')->orderBy('lib_title')->orderBy('number', 'asc')->get()");
      $this->assertNotEmpty($things);

      $things = Thing::where('lib_title', 'like', '%é%')
         ->orderBy('lib_title')
         ->orderBy('number', 'asc')
         ->get();
      $this->traceThingsWhere($things, "Thing::where('lib_title', 'like', '%é%')->orderBy('lib_title')->orderBy('number', 'asc')->get()");
      $this->assertNotEmpty($things);
   }
}
