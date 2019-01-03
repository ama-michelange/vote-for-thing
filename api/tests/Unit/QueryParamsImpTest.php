<?php
/**
 * Created by IntelliJ IDEA.
 * User: a454895
 * Date: 22/12/2018
 * Time: 01:30
 */

namespace Tests\Unit;

use Domain\Query\QueryParams;
use Domain\Query\QueryParamsImp;
use Tests\TestCase;

class QueryParamsImpTest extends TestCase
{
   const ARRAY_FIELD = ['myFieldOne'];
   const ARRAY_INCLUDE = ['myIncludeOne', 'myIncludeTwo'];
   const ARRAY_SORT = ['mySortOne', 'mySortTwo', 'mySortThree'];
   const ARRAY_DESC = ['myDescOne', 'myDescTwo', 'myDescThree', 'myDescFour'];
   const ARRAY_SEARCH = ['mySearchOne' => 'searchOne', 'mySearchTwo' => 'searchTwo', 'mySearchThree' => 'searchThree'];

   /**
    * @var \Domain\Query\QueryParamsImp
    */
   protected $queryParams;

   protected function setUp()
   {
      parent::setUp();
      $this->queryParams = new QueryParamsImp();
      $this->queryParams->put(QueryParams::LIMIT, 123);
      $this->queryParams->put(QueryParams::SKIP, 246);
      $this->queryParams->put(QueryParams::FIELD, QueryParamsImpTest::ARRAY_FIELD);
      $this->queryParams->put(QueryParams::INCLUDE, QueryParamsImpTest::ARRAY_INCLUDE);
      $this->queryParams->put(QueryParams::SORT, QueryParamsImpTest::ARRAY_SORT);
      $this->queryParams->put(QueryParams::DESC, QueryParamsImpTest::ARRAY_DESC);
   }

   public function testHas()
   {
      $this->assertTrue($this->queryParams->has(QueryParams::LIMIT));
      $this->assertTrue($this->queryParams->has(QueryParams::SKIP));
      $this->assertTrue($this->queryParams->has(QueryParams::FIELD));
      $this->assertTrue($this->queryParams->has(QueryParams::INCLUDE));
      $this->assertTrue($this->queryParams->has(QueryParams::SORT));
      $this->assertTrue($this->queryParams->has(QueryParams::DESC));
   }

   public function testGet()
   {
      $this->assertEquals(123, $this->queryParams->get(QueryParams::LIMIT));
      $this->assertEquals(246, $this->queryParams->get(QueryParams::SKIP));
      $this->assertEquals(QueryParamsImpTest::ARRAY_FIELD, $this->queryParams->get(QueryParams::FIELD));
      $this->assertEquals(QueryParamsImpTest::ARRAY_INCLUDE, $this->queryParams->get(QueryParams::INCLUDE));
      $this->assertEquals(QueryParamsImpTest::ARRAY_SORT, $this->queryParams->get(QueryParams::SORT));
      $this->assertEquals(QueryParamsImpTest::ARRAY_DESC, $this->queryParams->get(QueryParams::DESC));
   }

   public function testGetInt()
   {
      $this->assertEquals(123, $this->queryParams->getInt(QueryParams::LIMIT));
      $this->assertEquals(246, $this->queryParams->getInt(QueryParams::SKIP));
      $this->assertEquals(0, $this->queryParams->getInt(QueryParams::FIELD));
      $this->assertEquals(0, $this->queryParams->getInt(QueryParams::INCLUDE));
      $this->assertEquals(0, $this->queryParams->getInt(QueryParams::SORT));
      $this->assertEquals(0, $this->queryParams->getInt(QueryParams::DESC));
   }

   public function testGetArray()
   {
      $this->assertEquals([], $this->queryParams->getArray(QueryParams::LIMIT));
      $this->assertEquals([], $this->queryParams->getArray(QueryParams::SKIP));
      $this->assertEquals(QueryParamsImpTest::ARRAY_FIELD, $this->queryParams->getArray(QueryParams::FIELD));
      $this->assertEquals(QueryParamsImpTest::ARRAY_INCLUDE, $this->queryParams->getArray(QueryParams::INCLUDE));
      $this->assertEquals(QueryParamsImpTest::ARRAY_SORT, $this->queryParams->getArray(QueryParams::SORT));
      $this->assertEquals(QueryParamsImpTest::ARRAY_DESC, $this->queryParams->getArray(QueryParams::DESC));
   }

   public function testToArray()
   {
      $myArray = [
         QueryParams::LIMIT => 123,
         QueryParams::SKIP => 246,
         QueryParams::FIELD => QueryParamsImpTest::ARRAY_FIELD,
         QueryParams::INCLUDE => QueryParamsImpTest::ARRAY_INCLUDE,
         QueryParams::SORT => QueryParamsImpTest::ARRAY_SORT,
         QueryParams::DESC => QueryParamsImpTest::ARRAY_DESC
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
}
