<?php

namespace Tests\Unit;

use Domain\Entity\Category;
use Domain\Entity\Thing;
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
//      $this->artisan('db:migrate', ['--from' => 'json']);
      $this->artisan('db:migrate', ['--from' => 'json', '--quiet' => true]);
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

   /**
    * @test
    */
   public function differenceBetween_First_Get()
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

   /**
    * @test
    */
   public function differenceBetween_First_Get_With_OneToMany()
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

   /**
    * @test
    */
   public function where()
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

   private function traceThingsMessage($pThings, $pMessage)
   {
      $cpt = 0;
      Log::debug(sprintf('%s : %s', str_repeat('=', 20), $pMessage));
      foreach ($pThings as $value) {
         $cpt++;
         Log::debug(sprintf('%d) %s %s : %s - %s', $cpt, str_repeat('>', 5), $value->lib_title, $value->title, $value->number));
         Log::debug(print_r($value, true));
         Log::debug(sprintf('%d) %s', $cpt, str_repeat('<', 10)));
      }
   }

   /**
    * @test
    */
   public function select()
   {
      $things = Thing::where('lib_title', 'Batchalo')->select()->get();
      $this->traceThingsMessage($things, "Thing::where('lib_title', 'Batchalo')->select()->get()");
      $this->assertCount(1, $things);

      $things = Thing::where('lib_title', 'Batchalo')->select(['lib_title', 'id', 'image_url'])->get();
      $this->traceThingsMessage($things, "Thing::where('lib_title', 'Batchalo')->select(['lib_title','id','image_url'])->get()");
      $this->assertCount(1, $things);
   }

   /**
    * @test
    */
   public function attributesToArray()
   {
      $thing = new Thing();
      $attributes = $thing->attributesToArray();
      Log::debug(sprintf('%s : %s', str_repeat('=', 20), "thing"));
      Log::debug(print_r($thing, true));
      Log::debug(sprintf('%s : %s', str_repeat('=', 20), "thing->getVisible()"));
      Log::debug(print_r($thing->getVisible(), true));

      $diff_1 = array_diff(['id', 'title'], $thing->getVisible());
      Log::debug(sprintf('%s : %s', str_repeat('=', 20), "diff_1"));
      Log::debug(print_r($diff_1, true));
      $this->assertCount(0, $diff_1);

      $diff_2 = array_diff(['id', 'poule', 'title', 'papa'], $thing->getVisible());
      Log::debug(sprintf('%s : %s', str_repeat('=', 20), "diff_2"));
      Log::debug(print_r($diff_2, true));
      $this->assertCount(2, $diff_2);

      Log::debug(sprintf('%s : %s', str_repeat('=', 20), "testAttributesToArray"));
      Log::debug(print_r($attributes, true));
      $this->assertNotNull($attributes);

   }

   /**
    * @test
    */
   public function paginate()
   {
      $thingsPaginator = Thing::paginate(5);
      $this->assertCount(5, $thingsPaginator->items());

      $this->assertEquals(5, $thingsPaginator->count());
      $this->assertEquals(203, $thingsPaginator->total());
      $this->assertEquals(1, $thingsPaginator->firstItem());
      $this->assertEquals(5, $thingsPaginator->lastItem());
      $this->assertEquals(1, $thingsPaginator->currentPage());
      $this->assertEquals(5, $thingsPaginator->perPage());
      $this->assertEquals(41, $thingsPaginator->lastPage());
      $this->assertTrue($thingsPaginator->onFirstPage());
      $this->assertTrue($thingsPaginator->hasMorePages());

//      $thingsPaginator->withPath('custom/url');
//      Log::debug('>>>>>>>>>>>>>>>>>>>>>>>>>>> ' . "Thing::paginate(5, ['*'], 'page', 10)");
//      Log::debug('count()' . print_r($thingsPaginator->count(), true));
//      Log::debug('currentPage()' . print_r($thingsPaginator->currentPage(), true));
//      Log::debug('firstItem()' . print_r($thingsPaginator->firstItem(), true));
//      Log::debug('hasMorePages()' . print_r($thingsPaginator->hasMorePages(), true));
//      Log::debug('lastItem()' . print_r($thingsPaginator->lastItem(), true));
//      Log::debug('lastPage()' . print_r($thingsPaginator->lastPage(), true));
//      Log::debug('nextPageUrl()' . print_r($thingsPaginator->nextPageUrl(), true));
//      Log::debug('onFirstPage()' . print_r($thingsPaginator->onFirstPage(), true));
//      Log::debug('perPage()' . print_r($thingsPaginator->perPage(), true));
//      Log::debug('previousPageUrl()' . print_r($thingsPaginator->previousPageUrl(), true));
//      Log::debug('total()' . print_r($thingsPaginator->total(), true));
//      Log::debug('url(13)' . print_r($thingsPaginator->url(13), true));
   }

   /**
    * @test
    */
   public function paginate_With_All_Parameters()
   {
      $thingsPaginator = Thing::paginate(5, ['*'], 'page', 10);
      $this->assertCount(5, $thingsPaginator->items());

      $this->assertEquals(5, $thingsPaginator->count());
      $this->assertEquals(203, $thingsPaginator->total());
      $this->assertEquals(46, $thingsPaginator->firstItem());
      $this->assertEquals(50, $thingsPaginator->lastItem());
      $this->assertEquals(10, $thingsPaginator->currentPage());
      $this->assertEquals(5, $thingsPaginator->perPage());
      $this->assertEquals(41, $thingsPaginator->lastPage());
      $this->assertFalse($thingsPaginator->onFirstPage());
      $this->assertTrue($thingsPaginator->hasMorePages());

//      Log::debug('>>>>>>>>>>>>>>>>>>>>>>>>>>> ' . "Thing::paginate(5, ['*'], 'page', 10)");
//      Log::debug('count() : ' . print_r($thingsPaginator->count(), true));
//      Log::debug('currentPage() : ' . print_r($thingsPaginator->currentPage(), true));
//      Log::debug('firstItem() : ' . print_r($thingsPaginator->firstItem(), true));
//      Log::debug('hasMorePages() : ' . print_r($thingsPaginator->hasMorePages(), true));
//      Log::debug('lastItem() : ' . print_r($thingsPaginator->lastItem(), true));
//      Log::debug('lastPage() : ' . print_r($thingsPaginator->lastPage(), true));
//      Log::debug('nextPageUrl() : ' . print_r($thingsPaginator->nextPageUrl(), true));
//      Log::debug('onFirstPage() : ' . print_r($thingsPaginator->onFirstPage(), true));
//      Log::debug('perPage() : ' . print_r($thingsPaginator->perPage(), true));
//      Log::debug('previousPageUrl() : ' . print_r($thingsPaginator->previousPageUrl(), true));
//      Log::debug('total() : ' . print_r($thingsPaginator->total(), true));
//      Log::debug('url(13) : ' . print_r($thingsPaginator->url(13), true));
   }
}
