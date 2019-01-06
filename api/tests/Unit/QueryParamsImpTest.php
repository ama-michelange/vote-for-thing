<?php

namespace Tests\Unit;

use Domain\Query\QueryParams;
use Domain\Query\QueryParamsImp;
use Tests\TestCase;

class QueryParamsImpTest extends TestCase
{
   const INT_LIMIT = 123;
   const INT_SKIP = 246;
   const ARRAY_FIELD = ['myFieldOne'];
   const ARRAY_INCLUDE = ['myIncludeOne', 'myIncludeTwo'];
   const ARRAY_SORT = ['mySortOne', 'mySortTwo', 'mySortThree'];
   const ARRAY_DESC = ['myDescOne', 'myDescTwo', 'myDescThree', 'myDescFour'];
   const ARRAY_SEARCH = ['mySearchOne' => 'searchOne', 'mySearchTwo' => 'searchTwo', 'mySearchThree' => 'searchThree'];
   const STRING_USE_AS_ID = 'myUseAsId';

   /**
    * @var \Domain\Query\QueryParamsImp
    */
   protected $queryParams;

   protected function setUp()
   {
      parent::setUp();
      $this->queryParams = new QueryParamsImp();
      $this->queryParams->put(QueryParams::LIMIT, QueryParamsImpTest::INT_LIMIT);
      $this->queryParams->put(QueryParams::SKIP, QueryParamsImpTest::INT_SKIP);
      $this->queryParams->put(QueryParams::FIELD, QueryParamsImpTest::ARRAY_FIELD);
      $this->queryParams->put(QueryParams::INCLUDE, QueryParamsImpTest::ARRAY_INCLUDE);
      $this->queryParams->put(QueryParams::SORT, QueryParamsImpTest::ARRAY_SORT);
      $this->queryParams->put(QueryParams::DESC, QueryParamsImpTest::ARRAY_DESC);
      $this->queryParams->put(QueryParams::USE_AS_ID, QueryParamsImpTest::STRING_USE_AS_ID);
   }

   public function testHas()
   {
      $this->assertTrue($this->queryParams->has(QueryParams::LIMIT));
      $this->assertTrue($this->queryParams->has(QueryParams::SKIP));
      $this->assertTrue($this->queryParams->has(QueryParams::FIELD));
      $this->assertTrue($this->queryParams->has(QueryParams::INCLUDE));
      $this->assertTrue($this->queryParams->has(QueryParams::SORT));
      $this->assertTrue($this->queryParams->has(QueryParams::DESC));
      $this->assertTrue($this->queryParams->has(QueryParams::USE_AS_ID));
   }

   public function testGet()
   {
      $this->assertEquals(QueryParamsImpTest::INT_LIMIT, $this->queryParams->get(QueryParams::LIMIT));
      $this->assertEquals(QueryParamsImpTest::INT_SKIP, $this->queryParams->get(QueryParams::SKIP));
      $this->assertEquals(QueryParamsImpTest::ARRAY_FIELD, $this->queryParams->get(QueryParams::FIELD));
      $this->assertEquals(QueryParamsImpTest::ARRAY_INCLUDE, $this->queryParams->get(QueryParams::INCLUDE));
      $this->assertEquals(QueryParamsImpTest::ARRAY_SORT, $this->queryParams->get(QueryParams::SORT));
      $this->assertEquals(QueryParamsImpTest::ARRAY_DESC, $this->queryParams->get(QueryParams::DESC));
      $this->assertEquals(QueryParamsImpTest::STRING_USE_AS_ID, $this->queryParams->get(QueryParams::USE_AS_ID));
   }

   public function testGetInt()
   {
      $this->assertEquals(QueryParamsImpTest::INT_LIMIT, $this->queryParams->getInt(QueryParams::LIMIT));
      $this->assertEquals(QueryParamsImpTest::INT_SKIP, $this->queryParams->getInt(QueryParams::SKIP));
      $this->assertEquals(0, $this->queryParams->getInt(QueryParams::FIELD));
      $this->assertEquals(0, $this->queryParams->getInt(QueryParams::INCLUDE));
      $this->assertEquals(0, $this->queryParams->getInt(QueryParams::SORT));
      $this->assertEquals(0, $this->queryParams->getInt(QueryParams::DESC));
      $this->assertEquals(0, $this->queryParams->getInt(QueryParams::USE_AS_ID));
   }

   public function testGetString()
   {
      $this->assertEquals(QueryParamsImpTest::INT_LIMIT, $this->queryParams->getString(QueryParams::LIMIT));
      $this->assertEquals(QueryParamsImpTest::INT_SKIP, $this->queryParams->getString(QueryParams::SKIP));
      $this->assertEquals('', $this->queryParams->getString(QueryParams::FIELD));
      $this->assertEquals('', $this->queryParams->getString(QueryParams::INCLUDE));
      $this->assertEquals('', $this->queryParams->getString(QueryParams::SORT));
      $this->assertEquals('', $this->queryParams->getString(QueryParams::DESC));
      $this->assertEquals(QueryParamsImpTest::STRING_USE_AS_ID, $this->queryParams->getString(QueryParams::USE_AS_ID));
   }

   public function testGetArray()
   {
      $this->assertEquals([], $this->queryParams->getArray(QueryParams::LIMIT));
      $this->assertEquals([], $this->queryParams->getArray(QueryParams::SKIP));
      $this->assertEquals(QueryParamsImpTest::ARRAY_FIELD, $this->queryParams->getArray(QueryParams::FIELD));
      $this->assertEquals(QueryParamsImpTest::ARRAY_INCLUDE, $this->queryParams->getArray(QueryParams::INCLUDE));
      $this->assertEquals(QueryParamsImpTest::ARRAY_SORT, $this->queryParams->getArray(QueryParams::SORT));
      $this->assertEquals(QueryParamsImpTest::ARRAY_DESC, $this->queryParams->getArray(QueryParams::DESC));
      $this->assertEquals([], $this->queryParams->getArray(QueryParams::USE_AS_ID));
   }

   public function testToArray()
   {
      $myArray = [
         QueryParams::LIMIT => QueryParamsImpTest::INT_LIMIT,
         QueryParams::SKIP => QueryParamsImpTest::INT_SKIP,
         QueryParams::FIELD => QueryParamsImpTest::ARRAY_FIELD,
         QueryParams::INCLUDE => QueryParamsImpTest::ARRAY_INCLUDE,
         QueryParams::SORT => QueryParamsImpTest::ARRAY_SORT,
         QueryParams::DESC => QueryParamsImpTest::ARRAY_DESC,
         QueryParams::USE_AS_ID => QueryParamsImpTest::STRING_USE_AS_ID
      ];
      $this->assertEquals($myArray, $this->queryParams->toArray());
   }

   public function testHasAllFields()
   {
      $queryParams = new QueryParamsImp();
      $this->assertFalse($queryParams->hasAllFields());

      $queryParams->put(QueryParams::FIELD, QueryParamsImpTest::ARRAY_FIELD);
      $this->assertFalse($queryParams->hasAllFields());

      $queryParams->put(QueryParams::FIELD, ['*']);
      $this->assertTrue($queryParams->hasAllFields());
   }

   public function testHasLimit()
   {
      $queryParams = new QueryParamsImp();
      $this->assertFalse($queryParams->hasLimit());

      $queryParams->put(QueryParams::LIMIT, 0);
      $this->assertFalse($queryParams->hasLimit());

      $queryParams->put(QueryParams::LIMIT, 1);
      $this->assertTrue($queryParams->hasLimit());
   }

   public function testHasSearch()
   {
      $queryParams = new QueryParamsImp();
      $this->assertFalse($queryParams->hasSearch());

      $queryParams->put(QueryParams::SEARCH, QueryParamsImpTest::ARRAY_SEARCH);
      $this->assertTrue($queryParams->hasSearch());

      $queryParams->put(QueryParams::SEARCH, []);
      $this->assertTrue($queryParams->hasSearch());
   }

   public function testHasEmptySearch()
   {
      $queryParams = new QueryParamsImp();
      $this->assertFalse($queryParams->hasEmptySearch());

      $queryParams->put(QueryParams::SEARCH, QueryParamsImpTest::ARRAY_SEARCH);
      $this->assertFalse($queryParams->hasEmptySearch());

      $queryParams->put(QueryParams::SEARCH, []);
      $this->assertTrue($queryParams->hasEmptySearch());
   }

   public function testHasUseAsId()
   {
      $queryParams = new QueryParamsImp();
      $this->assertFalse($queryParams->hasUseAsId());

      $queryParams->put(QueryParams::USE_AS_ID, QueryParamsImpTest::STRING_USE_AS_ID);
      $this->assertTrue($queryParams->hasUseAsId());

      $queryParams->put(QueryParams::USE_AS_ID, '');
      $this->assertTrue($queryParams->hasUseAsId());

      $queryParams->put(QueryParams::USE_AS_ID, 123);
      $this->assertTrue($queryParams->hasUseAsId());
   }
}
