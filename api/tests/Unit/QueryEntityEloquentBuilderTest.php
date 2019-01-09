<?php


namespace Tests\Unit;


use Domain\Entity\Entity;
use Domain\Entity\Thing;
use Domain\Query\QueryParams;
use Domain\Query\QueryParamsImp;
use Illuminate\Database\Eloquent\Builder as IlluEloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Infra\EloquentInfraBuilder;
use Infra\Query\QueryEntityEloquentBuilder;
use Tests\TestCase;

class QueryEntityEloquentBuilderTest extends TestCase
{
   /**
    * @var QueryEntityEloquentBuilder
    */
   protected $realBuilder;

   /**
    * @var Entity
    */
   protected $realEntity;
   /**
    * @var array
    */
   protected $aVisible;
   /**
    * @var array
    */
   protected $aFillable;
   /**
    * @var array
    */
   protected $aAssociated;
   /**
    * @var array
    */
   protected $aSearch;
   /**
    * @var \Illuminate\Database\Query\Builder
    */
   protected $mockDatabaseQueryBuilder;
   /**
    * @var \Illuminate\Database\Eloquent\Builder
    */
   protected $mockDatabaseEloquentBuilder;
   /**
    * @var QueryEntityEloquentBuilder
    */
   protected $mockBuilder;

   /**
    * @before
    */
   protected function setupConstruct()
   {
      parent::setUp();

      // Real instances
      $this->realEntity = new Thing();
      $this->realBuilder = new QueryEntityEloquentBuilder($this->realEntity);

      // Extract some data of the entity to prepare tests
      $this->aVisible = $this->realEntity->getVisible();
      $this->aFillable = $this->realEntity->getFillable();
      $this->aAssociated = $this->realEntity->getAssociated();
      $this->aSearch = [];
      foreach ($this->aVisible as $field) {
         $this->aSearch[$field] = 'value_' . $field;
      }

      // Mock instances
      $this->mockDatabaseQueryBuilder = $this->getMockBuilder(QueryBuilder::class)->disableOriginalConstructor()->getMock();
      $this->mockDatabaseEloquentBuilder = $this->getMockBuilder(IlluEloquentBuilder::class)
         ->setConstructorArgs([$this->mockDatabaseQueryBuilder])
         ->setMethods(['select', 'skip', 'limit', 'with', 'orderBy', 'where'])
         ->getMock();
      $this->mockBuilder = new QueryEntityEloquentBuilder($this->realEntity, new EloquentInfraBuilder($this->mockDatabaseEloquentBuilder));
   }

   /**
    * @test
    */
   public function constructResult()
   {
      $this->assertNotNull($this->realBuilder->infraBuilder());
      $this->assertEquals('Infra\EloquentInfraBuilder', get_class($this->realBuilder->infraBuilder()));
   }

   /**
    * @test
    */
   public function build_Without_QueryParam()
   {
      $this->assertFalse($this->realBuilder->build());
   }

   /**
    * @test
    */
   public function build_With_QueryParams_Empty()
   {
      $queryParams = new QueryParamsImp();
      $this->mockBuilder->withParams($queryParams);

      $this->mockDatabaseEloquentBuilder->expects($this->never())->method('select');
      $this->mockDatabaseEloquentBuilder->expects($this->never())->method('with');
      $this->mockDatabaseEloquentBuilder->expects($this->never())->method('orderBy');
      $this->mockDatabaseEloquentBuilder->expects($this->never())->method('where');

      $this->assertNotNull($this->mockBuilder->build());
   }

   /**
    * @test
    */
   public function build_When_Verify_Return_True()
   {
      $mock = $this->getMockBuilder(QueryEntityEloquentBuilder::class)
         ->setConstructorArgs([new Thing()])
         ->setMethods(['verify', 'buildParams'])
         ->getMock();
      $mock->expects($this->once())->method('verify')->willReturn(true);
      $mock->expects($this->once())->method('buildParams');
      $this->assertNotNull($mock->build());
   }

   /**
    * @test
    */
   public function build_When_Verify_Return_False()
   {
      $mock = $this->getMockBuilder(QueryEntityEloquentBuilder::class)
         ->setConstructorArgs([new Thing()])
         ->setMethods(['verify', 'buildParams'])
         ->getMock();
      $mock->expects($this->once())->method('verify')->willReturn(false);
      $mock->expects($this->never())->method('buildParams');
      $this->assertFalse($mock->build());
   }

   /**
    * @test
    */
   public function build_When_Complete_Parameters()
   {
      $queryParams = new QueryParamsImp();
      $queryParams->put(QueryParams::FIELD, $this->aVisible);
      $queryParams->put(QueryParams::LIMIT, 23);
      $queryParams->put(QueryParams::SKIP, 107);
      $queryParams->put(QueryParams::INCLUDE, $this->aAssociated);
      $queryParams->put(QueryParams::SORT, $this->aVisible);
      $queryParams->put(QueryParams::DESC, $this->aVisible);
      $queryParams->put(QueryParams::SEARCH, $this->aSearch);
      $queryParams->put(QueryParams::USE_AS_ID, [$this->aVisible[0], 'value_' . $this->aVisible[0]]);

      $this->mockDatabaseEloquentBuilder->expects($this->once())
         ->method('select')
         ->with($this->aVisible);
      $this->mockDatabaseEloquentBuilder->expects($this->once())
         ->method('limit')
         ->with(23);
      $this->mockDatabaseEloquentBuilder->expects($this->once())
         ->method('skip')
         ->with(107);
      $this->mockDatabaseEloquentBuilder->expects($this->once())
         ->method('with')
         ->with($this->aAssociated);
      $this->mockDatabaseEloquentBuilder->expects($this->exactly(count($this->aVisible)))
         ->method('orderBy')
         ->with($this->callback(function ($value) {
            return in_array($value, $this->aVisible);
         }), 'desc');
      $this->mockDatabaseEloquentBuilder->expects($this->exactly(count($this->aVisible) + 1))->method('where');

      $this->mockBuilder->withParams($queryParams);
      $this->assertNotNull($this->mockBuilder->build());
   }

   /**
    * @test
    */
   public function build_When_Field_With_All()
   {
      $queryParams = new QueryParamsImp();
      $queryParams->put(QueryParams::FIELD, ['*']);
      $this->mockBuilder->withParams($queryParams);

      $this->mockDatabaseEloquentBuilder->expects($this->never())->method('select');
      $this->assertNotNull($this->mockBuilder->build());
   }

   /**
    * @test
    */
   public function build_When_Field_With_Only_Fillable_Fields()
   {
      $queryParams = new QueryParamsImp();
      $queryParams->put(QueryParams::FIELD, $this->aFillable);

      $this->mockDatabaseEloquentBuilder->expects($this->once())
         ->method('select')
         ->with($this->aFillable);

      $this->mockBuilder->withParams($queryParams);
      $this->assertNotNull($this->mockBuilder->build());
   }

   /**
    * @test
    *
    * @expectedException \DomainException
    * @expectedExceptionMessage Unknown field : foo
    */
   public function build_When_Field_With_Bad_Field()
   {
      $queryParams = new QueryParamsImp();
      $queryParams->put(QueryParams::FIELD, ['foo']);
      $this->mockBuilder->withParams($queryParams)->build();
   }

   /**
    * @test
    *
    * @expectedException \DomainException
    * @expectedExceptionMessage Unknown object to include : foo
    */
   public function build_When_Include_With_Bad_Associate()
   {
      $queryParams = new QueryParamsImp();
      $queryParams->put(QueryParams::INCLUDE, ['foo']);
      $this->mockBuilder->withParams($queryParams)->build();
   }

   /**
    * @test
    */
   public function build_When_Sort_With_Mixed_Asc_And_Desc()
   {
      $queryParams = new QueryParamsImp();
      $queryParams->put(QueryParams::SORT, ['id', 'title', 'number']);
      $queryParams->put(QueryParams::DESC, ['title']);

      $this->mockDatabaseEloquentBuilder->expects($this->exactly(3))
         ->method('orderBy')
         ->withConsecutive(['id', 'asc'], ['title', 'desc'], ['number', 'asc']);

      $this->mockBuilder->withParams($queryParams);
      $this->assertNotNull($this->mockBuilder->build());
   }

   /**
    * @test
    *
    * @expectedException \DomainException
    * @expectedExceptionMessage Unknown field to sort : foo
    */
   public function build_When_Sort_With_Bad_Field()
   {
      $queryParams = new QueryParamsImp();
      $queryParams->put(QueryParams::SORT, ['foo']);
      $this->mockBuilder->withParams($queryParams)->build();
   }

   /**
    * @test
    *
    * @expectedException \DomainException
    * @expectedExceptionMessage Unknown field to descendant sort : foo
    */
   public function build_When_Desc_With_Bad_Field()
   {
      $queryParams = new QueryParamsImp();
      $queryParams->put(QueryParams::DESC, ['foo']);
      $this->mockBuilder->withParams($queryParams)->build();
   }

   /**
    * @test
    *
    * @expectedException \DomainException
    * @expectedExceptionMessage Unknown field to search : foo
    */
   public function build_When_Search_With_Bad_Field()
   {
      $queryParams = new QueryParamsImp();
      $queryParams->put(QueryParams::SEARCH, ['foo' => 'fooValue']);
      $this->mockBuilder->withParams($queryParams)->build();
   }

   /**
    * @test
    *
    * @expectedException \DomainException
    * @expectedExceptionMessage No field to search
    */
   public function build_When_Search_Without_Field()
   {
      $queryParams = new QueryParamsImp();
      $queryParams->put(QueryParams::SEARCH, []);
      $this->mockBuilder->withParams($queryParams)->build();
   }

   /**
    * @test
    */
   public function build_When_Search_With_like()
   {
      $queryParams = new QueryParamsImp();
      $queryParams->put(QueryParams::SEARCH, [
         'lib_title' => 'ast*',
         'title' => 'or **ast**',
         'proper_title' => '*dom*',
      ]);

      $this->mockDatabaseEloquentBuilder->expects($this->exactly(3))
         ->method('where')
         ->withConsecutive(
            ['lib_title', 'like', 'ast%', 'and'],
            ['title', 'like', '%ast%', 'or'],
            ['proper_title', 'like', '%dom%', 'and']
         );

      $this->mockBuilder->withParams($queryParams);
      $this->assertNotNull($this->mockBuilder->build());
   }

   /**
    * @test
    */
   public function build_When_UseAsId_With_A_Good_Field()
   {
      $queryParams = new QueryParamsImp();
      $queryParams->put(QueryParams::USE_AS_ID, [$this->aVisible[0], 10]);

      $this->mockDatabaseEloquentBuilder->expects($this->never())->method('select');
      $this->mockDatabaseEloquentBuilder->expects($this->never())->method('with');
      $this->mockDatabaseEloquentBuilder->expects($this->never())->method('orderBy');
      $this->mockDatabaseEloquentBuilder->expects($this->never())->method('where');

      $this->mockBuilder->withParams($queryParams);
      $this->assertNotNull($this->mockBuilder->build());
   }

   /**
    * @test
    *
    * @expectedException \DomainException
    * @expectedExceptionMessage Unknown field to use as id : foo
    */
   public function build_When_UseAsId_With_A_Bad_Field()
   {
      $queryParams = new QueryParamsImp();
      $queryParams->put(QueryParams::USE_AS_ID, ['foo', 10]);
      $this->mockBuilder->withParams($queryParams)->build();
   }

   /**
    * @test
    *
    * @expectedException \DomainException
    * @expectedExceptionMessage Use as id : value mandatory
    */
   public function build_When_UseAsId_With_A_Good_Field_And_Without_Value()
   {
      $queryParams = new QueryParamsImp();
      $queryParams->put(QueryParams::USE_AS_ID, [$this->aVisible[0]]);
      $this->mockBuilder->withParams($queryParams)->build();
   }


}
