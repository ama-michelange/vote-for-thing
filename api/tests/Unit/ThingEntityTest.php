<?php

namespace Tests\Unit;

use BadMethodCallException;
use Domain\Entity\Category;
use Domain\Entity\ThingEntity;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ThingEntityTest extends TestCase
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
   public function study_differenceBetween_First_Get()
   {
      $this->assertNotNull(ThingEntity::where('lib_title', 'Batchalo')->first());
      $this->assertEquals(ThingEntity::class, get_class(ThingEntity::where('lib_title', 'Batchalo')->first()));
      $this->assertEquals('Batchalo', ThingEntity::where('lib_title', 'Batchalo')->first()->title);

      $this->assertNotNull(ThingEntity::where('lib_title', 'Batchalo')->get());
      $this->assertCount(1, ThingEntity::where('lib_title', 'Batchalo')->get());
      $this->assertEquals(Collection::class, get_class(ThingEntity::where('lib_title', 'Batchalo')->get()));

      $this->assertNotNull('Batchalo', ThingEntity::where('lib_title', 'Batchalo')->get()[0]);
      $this->assertEquals(ThingEntity::class, get_class(ThingEntity::where('lib_title', 'Batchalo')->get()[0]));
      $this->assertEquals('Batchalo', ThingEntity::where('lib_title', 'Batchalo')->get()[0]->title);

      $this->assertNotNull(
         'Batchalo',
         ThingEntity::where('lib_title', 'Batchalo')
            ->get()
            ->get(0)
      );
      $this->assertEquals(
         ThingEntity::class,
         get_class(
            ThingEntity::where('lib_title', 'Batchalo')
               ->get()
               ->get(0)
         )
      );
      $this->assertEquals(
         'Batchalo',
         ThingEntity::where('lib_title', 'Batchalo')
            ->get()
            ->get(0)->title
      );
   }

   /**
    * @test
    */
   public function study_differenceBetween_First_Get_With_OneToMany()
   {
      $thing = ThingEntity::where('lib_title', 'Batchalo')->first();

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
   public function study_where()
   {
      $things = ThingEntity::where('lib_title', 'Batchalo')->get();
      // $this->traceThingsWhere($things, "Thing::where('lib_title', 'Batchalo')->get()");
      $this->assertCount(1, $things);

      $things = ThingEntity::where('lib_title', 'like', 'B%')->get();
      // $this->traceThingsWhere($things, "Thing::where('lib_title', 'like', 'B%')->get()");
      $this->assertNotEmpty($things);

      $things = ThingEntity::where('lib_title', 'like', 'B%')
         ->orderBy('lib_title')
         ->get();
      // $this->traceThingsWhere($things, "Thing::where('lib_title', 'like', 'B%')->orderBy('lib_title')->get()");
      $this->assertNotEmpty($things);

      $things = ThingEntity::where('lib_title', 'like', 'a%')
         ->orderBy('lib_title')
         ->orderBy('number', 'desc')
         ->get();
      // $this->traceThingsWhere($things, "Thing::where('lib_title', 'like', 'a%')->orderBy('lib_title')->orderBy('number', 'desc')->get()");
      $this->assertNotEmpty($things);

      $things = ThingEntity::where('lib_title', 'like', '%b%')
         ->orderBy('lib_title')
         ->orderBy('number', 'asc')
         ->get();
      // $this->traceThingsWhere($things, "Thing::where('lib_title', 'like', '%b%')->orderBy('lib_title')->orderBy('number', 'asc')->get()");
      $this->assertNotEmpty($things);

      $things = ThingEntity::where('lib_title', 'like', '%é%')
         ->orderBy('lib_title')
         ->orderBy('number', 'asc')
         ->get();
      // $this->traceThingsWhere($things, "Thing::where('lib_title', 'like', '%é%')->orderBy('lib_title')->orderBy('number', 'asc')->get()");
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
   public function study_select()
   {
      $things = ThingEntity::where('lib_title', 'Batchalo')->select()->get();
      // $this->traceThingsMessage($things, "Thing::where('lib_title', 'Batchalo')->select()->get()");
      $this->assertCount(1, $things);

      $things = ThingEntity::where('lib_title', 'Batchalo')->select(['lib_title', 'id', 'image_url'])->get();
      // $this->traceThingsMessage($things, "Thing::where('lib_title', 'Batchalo')->select(['lib_title','id','image_url'])->get()");
      $this->assertCount(1, $things);
   }

   /**
    * @group current
    * @test
    */
   public function study_attributesToArray()
   {
      $thing = new ThingEntity();
      $attributes = $thing->attributesToArray();

//      Log::debug(sprintf('%s : %s', str_repeat('=', 20), "thing"));
//      Log::debug(print_r($thing, true));

//      Log::debug(sprintf('%s : %s', str_repeat('=', 20), "thing->attributesToArray()"));
//      Log::debug(print_r($thing->attributesToArray(), true));
//
//      Log::debug(sprintf('%s : %s', str_repeat('=', 20), "thing->getFillable()"));
//      Log::debug(print_r($thing->getFillable(), true));
//
//      Log::debug(sprintf('%s : %s', str_repeat('=', 20), "thing->getVisible()"));
//      Log::debug(print_r($thing->getVisible(), true));
//
//      Log::debug(sprintf('%s : %s', str_repeat('=', 20), "thing->getHidden()"));
//      Log::debug(print_r($thing->getHidden(), true));

      $diff_1 = array_diff(['id', 'title'], $thing->getVisible());
//      Log::debug(sprintf('%s : %s', str_repeat('=', 20), "diff_1"));
//      Log::debug(print_r($diff_1, true));
      $this->assertCount(0, $diff_1);

      $diff_2 = array_diff(['id', 'poule', 'title', 'papa'], $thing->getVisible());
//      Log::debug(sprintf('%s : %s', str_repeat('=', 20), "diff_2"));
//      Log::debug(print_r($diff_2, true));
      $this->assertCount(2, $diff_2);

//      Log::debug(sprintf('%s : %s', str_repeat('=', 20), "testAttributesToArray"));
//      Log::debug(print_r($attributes, true));
      $this->assertNotNull($attributes);
   }

   /**
    * @test
    */
   public function study_paginate()
   {
      $thingsPaginator = ThingEntity::paginate(5);
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
   public function study_paginate_With_All_Parameters()
   {
      $thingsPaginator = ThingEntity::paginate(5, ['*'], 'page', 10);
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

   /**
    * @group current
    * @test
    */
   public function study_create()
   {
//      Log::debug('>>>>>>>>>>>>>>>>>>>>>>>>>>> ' . "Thing::create(data)");
      $data = ['title' => 'sally', 'lib_title' => 'libSally', 'category_id' => 1];

      $thing = new ThingEntity();
      $cat = Category::find($data['category_id']);
//      Log::debug('cat >>>> ' . print_r($cat, true));

      $thing->category()->associate($cat);
//      Log::debug('thing >>>> ' . print_r($thing, true));

      $thing->fill($data);
      $thing->save();

//      Log::debug('FIRST >>>> ' . print_r($thing, true));

      $this->assertNotNull($thing->id);
      $this->assertNotNull($thing->category_id);
      $this->assertNotNull($thing->category);
      $this->assertEquals('comic', $thing->category->name);

      $other = ThingEntity::find($thing->id);
      $this->assertNotNull($other);
      $this->assertEquals($thing->id, $other->id);
      $this->assertEquals($data['title'], $thing->title);
      $this->assertEquals('comic', $other->category->name);

   }

   /**
    * @group current
    * @test
    */
   public function study_create_with_category_id()
   {
//      Log::debug('>>>>>>>>>>>>>>>>>>>>>>>>>>> ' . "Thing::create(data)");
      $data = ['title' => 'sally', 'lib_title' => 'libSally', 'category_id' => 1];

      $thing = ThingEntity::create($data);

//      Log::debug('FIRST >>>> ' . print_r($thing, true));

      $this->assertNotNull($thing->id);
      $this->assertNotNull($thing->category_id);
      $this->assertNotNull($thing->category);
      $this->assertEquals('comic', $thing->category->name);

      $other = ThingEntity::find($thing->id);
      $this->assertNotNull($other);
      $this->assertEquals($thing->id, $other->id);
      $this->assertEquals($data['title'], $thing->title);
      $this->assertEquals('comic', $other->category->name);
      $this->assertEquals($thing->category_id, $other->category_id);

   }

   /**
    * @group current
    * @test
    */
   public function given_ThingEntity_when_new_instance()
   {
      $thing = new ThingEntity();
      $this->assertCount(0, $thing->attributesToArray());
      $this->assertCount(9, $thing->getFillable());
      $this->assertCount(1, $thing->getHidden());
      $this->assertCount(11, $thing->getVisible());

      $fillable = [
         'title',
         'lib_title',
         'proper_title',
         'number',
         'image_url',
         'description_url',
         'legal',
         'description',
         'category_id',
      ];
      $this->assertEquals($fillable, $thing->getFillable());

      $hidden = ['category_id'];
      $this->assertEquals($hidden, $thing->getHidden());

      $visible = [
         'title',
         'lib_title',
         'proper_title',
         'number',
         'image_url',
         'description_url',
         'legal',
         'description',
         'id',
         'created_at',
         'updated_at',
      ];
      $this->assertEquals($visible, $thing->getVisible());

   }

   /**
    * @group current
    * @test
    */
   public function given_ThingEntity_when_findAssociatedForeignKey()
   {
      $thing = new ThingEntity();
      $this->assertEquals('category_id', $thing->findAssociatedForeignKey('category'));
   }

   /**
    * @group current
    * @test
    * @expectedException BadMethodCallException
    */
   public function given_ThingEntity_when_findAssociatedForeignKey_with_unknown_associate()
   {
      $thing = new ThingEntity();
      $thing->findAssociatedForeignKey('unknown');
   }

   /**
    * @group current
    * @test
    */
   public function given_ThingEntity_when_findAllAssociatedForeignKey()
   {
      $thing = new ThingEntity();
      $this->assertEquals(['category_id'], $thing->findAllAssociatedForeignKey());
   }
}
