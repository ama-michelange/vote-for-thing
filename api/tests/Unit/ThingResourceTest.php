<?php

namespace Tests\Unit;

use App\Http\Resources\ThingResource;
use Tests\TestCase;


class ThingResourceTest extends TestCase
{

   /**
    * @var \App\Http\Resources\Resource
    */
   protected $resource;

   protected function setUp()
   {
      parent::setUp();
      $this->resource = new ThingResource();
   }

   /**
    * @test
    */
   public function given_ThingResource_when_entity()
   {
      $this->assertNotNull($this->resource->entity());
      $this->assertEquals('Domain\Entity\ThingEntity', get_class($this->resource->entity()));
      $call1 = $this->resource->entity();
      $call2 = $this->resource->entity();
      $this->assertSame($call1, $call2);
   }

   /**
    * @test
    */
   public function given_ThingResource_when_fields()
   {
      $this->assertNotNull($this->resource->fields());
   }

   /**
    * @test
    */
   public function given_ThingResource_when_query()
   {
      $this->assertNotNull($this->resource->query());
      $call1 = $this->resource->query();
      $call2 = $this->resource->query();
      $this->assertSame($call1, $call2);
   }

   /**
    * @test
    */
   public function given_ThingResource_when_command()
   {
      $this->markTestIncomplete(
         'This test has not been implemented yet.'
      );
      $this->assertNotNull($this->resource->command());
      $call1 = $this->resource->command();
      $call2 = $this->resource->command();
      $this->assertSame($call1, $call2);
   }
}
