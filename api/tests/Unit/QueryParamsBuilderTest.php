<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\QueryParamsBuilder;
use App\Http\Resources\Resource;
use Domain\Entity\Entity;
use Domain\Query\QueryParams;
use Illuminate\Http\Request;
use Tests\TestCase;

class QueryParamsBuilderTest extends TestCase
{
   /**
    * QueryString complete with maximum parameters
    */
   const MAXIMUM_PARAMETERS = [
      'limit' => 21,
      'skip' => 123,
      'field' => 'field_one',
      'include' => 'include_one',
      'sort' => 'sort_one',
      'desc' => 'desc_one',
      'name' => 'name_value',
      'title' => 'title_value',
      'use_as_id' => 'use_as_id_value'
   ];

   /**
    * @var \Illuminate\Http\Request
    */
   protected $request;

   /**
    * @var \App\Http\Resources\Resource
    */
   protected $resource;

   protected function setUp()
   {
      parent::setUp();
      $this->request = new Request();
      $this->resource = new MyResource();
   }

   /**
    * @test
    */
   public function given_QueryParamsBuilder_when_build_without_initialized()
   {
      $params = (new QueryParamsBuilder($this->request))->build();
      $this->assertFalse($params->has(QueryParams::LIMIT));
      $this->assertFalse($params->has(QueryParams::SKIP));
      $this->assertFalse($params->has(QueryParams::FIELD));
      $this->assertFalse($params->has(QueryParams::INCLUDE));
      $this->assertFalse($params->has(QueryParams::SORT));
      $this->assertFalse($params->has(QueryParams::DESC));
      $this->assertFalse($params->has(QueryParams::USE_AS_ID));
   }

   /**
    * @test
    */
  public function given_QueryParamsBuilder_when_withLimit()
   {
      // QueryString empty
      $params = (new QueryParamsBuilder($this->request))->withLimit()->build();
      $this->assertTrue($params->has(QueryParams::LIMIT));
      $this->assertEquals(0, $params->getInt(QueryParams::LIMIT));

      // QueryString with limit
      $this->request->replace(['limit' => 123]);
      $params = (new QueryParamsBuilder($this->request))->withLimit()->build();
      $this->assertTrue($params->has(QueryParams::LIMIT));
      $this->assertEquals(123, $params->getInt(QueryParams::LIMIT));

      // QueryString with string number limit
      $this->request->replace(['limit' => '256']);
      $params = (new QueryParamsBuilder($this->request))->withLimit()->build();
      $this->assertTrue($params->has(QueryParams::LIMIT));
      $this->assertEquals(256, $params->getInt(QueryParams::LIMIT));

      // QueryString with string limit
      $this->request->replace(['limit' => 'string']);
      $params = (new QueryParamsBuilder($this->request))->withLimit()->build();
      $this->assertTrue($params->has(QueryParams::LIMIT));
      $this->assertEquals(0, $params->getInt(QueryParams::LIMIT));
      $this->assertEquals(0, $params->get(QueryParams::LIMIT));
   }

   /**
    * @test
    */
   public function given_MyDefaultMaximumBuilder_when_withLimit()
   {
      // QueryString empty
      $params = (new MyDefaultMaximumBuilder($this->request))->withLimit()->build();
      $this->assertTrue($params->has(QueryParams::LIMIT));
      $this->assertEquals(77, $params->getInt(QueryParams::LIMIT));

      // QueryString with limit
      $this->request->replace(['limit' => 123]);
      $params = (new MyDefaultMaximumBuilder($this->request))->withLimit()->build();
      $this->assertTrue($params->has(QueryParams::LIMIT));
      $this->assertEquals(123, $params->getInt(QueryParams::LIMIT));

      // QueryString with limit
      $this->request->replace(['limit' => 1000]);
      $params = (new MyDefaultMaximumBuilder($this->request))->withLimit()->build();
      $this->assertTrue($params->has(QueryParams::LIMIT));
      $this->assertEquals(500, $params->getInt(QueryParams::LIMIT));
   }

   /**
    * @test
    */
   public function given_QueryParamsBuilder_when_withSkip()
   {
      // QueryString empty
      $params = (new QueryParamsBuilder($this->request))->withSkip()->build();
      $this->assertTrue($params->has(QueryParams::SKIP));
      $this->assertEquals(0, $params->getInt(QueryParams::SKIP));

      // QueryString with skip
      $this->request->replace(['skip' => 123]);
      $params = (new QueryParamsBuilder($this->request))->withSkip()->build();
      $this->assertTrue($params->has(QueryParams::SKIP));
      $this->assertEquals(123, $params->getInt(QueryParams::SKIP));

      // QueryString with string number skip
      $this->request->replace(['skip' => '255']);
      $params = (new QueryParamsBuilder($this->request))->withSkip()->build();
      $this->assertTrue($params->has(QueryParams::SKIP));
      $this->assertEquals(255, $params->getInt(QueryParams::SKIP));

      // QueryString with string skip
      $this->request->replace(['skip' => 'string']);
      $params = (new QueryParamsBuilder($this->request))->withSkip()->build();
      $this->assertTrue($params->has(QueryParams::SKIP));
      $this->assertEquals(0, $params->getInt(QueryParams::SKIP));
      $this->assertEquals(0, $params->get(QueryParams::SKIP));
   }

   /**
    * @test
    */
   public function given_QueryParamsBuilder_when_withField()
   {
      // QueryString empty
      $params = (new QueryParamsBuilder($this->request))->withField()->build();
      $this->assertTrue($params->has(QueryParams::FIELD));
      $this->assertEquals(['*'], $params->get(QueryParams::FIELD));
      $this->assertEquals(['*'], $params->getArray(QueryParams::FIELD));
      $this->assertEquals(0, $params->getInt(QueryParams::FIELD));

      // QueryString with one field
      $this->request->replace(['field' => 'one']);
      $params = (new QueryParamsBuilder($this->request))->withField()->build();
      $this->assertTrue($params->has(QueryParams::FIELD));
      $this->assertEquals(['one'], $params->getArray(QueryParams::FIELD));

      // QueryString with two fields
      $this->request->replace(['field' => 'one,two']);
      $params = (new QueryParamsBuilder($this->request))->withField()->build();
      $this->assertTrue($params->has(QueryParams::FIELD));
      $this->assertEquals(['one', 'two'], $params->getArray(QueryParams::FIELD));

      // QueryString with two fields with space
      $this->request->replace(['field' => ' one , two ']);
      $params = (new QueryParamsBuilder($this->request))->withField()->build();
      $this->assertTrue($params->has(QueryParams::FIELD));
      $this->assertEquals(['one', 'two'], $params->getArray(QueryParams::FIELD));

      // QueryString with multiple includes with special words and space
      $this->request->replace(['field' => 'kebab-one, kebab-two , ,space one, space two , ,snake_one, snake_two ']);
      $params = (new QueryParamsBuilder($this->request))->withField()->build();
      $this->assertTrue($params->has(QueryParams::FIELD));
      $this->assertEquals(['kebab-one', 'kebab-two', 'space one', 'space two', 'snake_one', 'snake_two'], $params->getArray(QueryParams::FIELD));


      // QueryString with bad integer field
      $this->request->replace(['field' => 123]);
      $params = (new QueryParamsBuilder($this->request))->withField()->build();
      $this->assertTrue($params->has(QueryParams::FIELD));
      $this->assertEquals(['*'], $params->get(QueryParams::FIELD));
      $this->assertEquals(['*'], $params->getArray(QueryParams::FIELD));
      $this->assertEquals(0, $params->getInt(QueryParams::FIELD));
   }

   /**
    * @test
    */
   public function given_QueryParamsBuilder_when_withInclude()
   {
      // QueryString empty
      $params = (new QueryParamsBuilder($this->request))->withInclude()->build();
      $this->assertFalse($params->has(QueryParams::INCLUDE));

      // QueryString with one include
      $this->request->replace(['include' => 'one']);
      $params = (new QueryParamsBuilder($this->request))->withInclude()->build();
      $this->assertTrue($params->has(QueryParams::INCLUDE));
      $this->assertEquals(['one'], $params->getArray(QueryParams::INCLUDE));

      // QueryString with two includes
      $this->request->replace(['include' => 'one,two']);
      $params = (new QueryParamsBuilder($this->request))->withInclude()->build();
      $this->assertTrue($params->has(QueryParams::INCLUDE));
      $this->assertEquals(['one', 'two'], $params->getArray(QueryParams::INCLUDE));

      // QueryString with multiple includes with special words and space
      $this->request->replace(['include' => 'kebab-one, kebab-two , ,space one, space two , ,snake_one, snake_two ']);
      $params = (new QueryParamsBuilder($this->request))->withInclude()->build();
      $this->assertTrue($params->has(QueryParams::INCLUDE));
      $this->assertEquals(['kebabOne', 'kebabTwo', 'spaceOne', 'spaceTwo', 'snakeOne', 'snakeTwo'], $params->getArray(QueryParams::INCLUDE));

      // QueryString with bad integer include
      $this->request->replace(['include' => 123]);
      $params = (new QueryParamsBuilder($this->request))->withInclude()->build();
      $this->assertFalse($params->has(QueryParams::INCLUDE));
   }

   /**
    * @test
    */
   public function given_QueryParamsBuilder_when_withSort()
   {
      // QueryString empty
      $params = (new QueryParamsBuilder($this->request))->withSort()->build();
      $this->assertFalse($params->has(QueryParams::SORT));

      // QueryString with one sort
      $this->request->replace(['sort' => 'one']);
      $params = (new QueryParamsBuilder($this->request))->withSort()->build();
      $this->assertTrue($params->has(QueryParams::SORT));
      $this->assertEquals(['one'], $params->getArray(QueryParams::SORT));

      // QueryString with two sorts
      $this->request->replace(['sort' => 'one,two']);
      $params = (new QueryParamsBuilder($this->request))->withSort()->build();
      $this->assertTrue($params->has(QueryParams::SORT));
      $this->assertEquals(['one', 'two'], $params->getArray(QueryParams::SORT));

      // QueryString with two sorts with space
      $this->request->replace(['sort' => ' one , two ']);
      $params = (new QueryParamsBuilder($this->request))->withSort()->build();
      $this->assertTrue($params->has(QueryParams::SORT));
      $this->assertEquals(['one', 'two'], $params->getArray(QueryParams::SORT));

      // QueryString with multiple includes with special words and space
      $this->request->replace(['sort' => 'kebab-one, kebab-two , ,space one, space two , ,snake_one, snake_two ']);
      $params = (new QueryParamsBuilder($this->request))->withSort()->build();
      $this->assertTrue($params->has(QueryParams::SORT));
      $this->assertEquals(['kebab-one', 'kebab-two', 'space one', 'space two', 'snake_one', 'snake_two'], $params->getArray(QueryParams::SORT));

      // QueryString with bad integer sort
      $this->request->replace(['sort' => 123]);
      $params = (new QueryParamsBuilder($this->request))->withSort()->build();
      $this->assertFalse($params->has(QueryParams::SORT));
   }

   /**
    * @test
    */
   public function given_QueryParamsBuilder_when_withSortDesc()
   {
      // QueryString empty
      $params = (new QueryParamsBuilder($this->request))->withSortDesc()->build();
      $this->assertFalse($params->has(QueryParams::DESC));

      // QueryString with one desc
      $this->request->replace(['desc' => 'one']);
      $params = (new QueryParamsBuilder($this->request))->withSortDesc()->build();
      $this->assertFalse($params->has(QueryParams::DESC));

      // QueryString with one sort and one desc
      $this->request->replace(['sort' => 'sort_one', 'desc' => 'desc_one']);
      $params = (new QueryParamsBuilder($this->request))->withSortDesc()->build();
      $this->assertTrue($params->has(QueryParams::DESC));
      $this->assertEquals(['desc_one'], $params->getArray(QueryParams::DESC));
      $this->assertTrue($params->has(QueryParams::SORT));
      $this->assertEquals(['sort_one'], $params->getArray(QueryParams::SORT));

      // QueryString with multiple sorts and same descs
      $this->request->replace(['sort' => 'sort_one,sort_two', 'desc' => 'desc_one,desc_two']);
      $params = (new QueryParamsBuilder($this->request))->withSortDesc()->build();
      $this->assertTrue($params->has(QueryParams::DESC));
      $this->assertEquals(['desc_one', 'desc_two'], $params->getArray(QueryParams::DESC));
      $this->assertTrue($params->has(QueryParams::SORT));
      $this->assertEquals(['sort_one', 'sort_two'], $params->getArray(QueryParams::SORT));

      // QueryString with multiple sorts and descs with special words and space
      $this->request->replace([
            'sort' => 'sort-kebab-one, sort-kebab-two , ,sort space one, sort space two , ,sort_snake_one, sort_snake_two ',
            'desc' => 'desc-kebab-one, desc-kebab-two , ,desc space one, desc space two , ,desc_snake_one, desc_snake_two ']
      );
      $params = (new QueryParamsBuilder($this->request))->withSortDesc()->build();
      $this->assertTrue($params->has(QueryParams::DESC));
      $this->assertEquals(
         ['desc-kebab-one', 'desc-kebab-two', 'desc space one', 'desc space two', 'desc_snake_one', 'desc_snake_two'],
         $params->getArray(QueryParams::DESC));

      // QueryString with sorts and bad integer desc
      $this->request->replace(['sort' => 'sort_one, sort_two', 'desc' => 123]);
      $params = (new QueryParamsBuilder($this->request))->withSortDesc()->build();
      $this->assertTrue($params->has(QueryParams::DESC));
      $this->assertEquals(['sort_one'], $params->getArray(QueryParams::DESC));

      // QueryString with sorts and empty string desc
      $this->request->replace(['sort' => 'sort_one, sort_two', 'desc' => '']);
      $params = (new QueryParamsBuilder($this->request))->withSortDesc()->build();
      $this->assertTrue($params->has(QueryParams::DESC));
      $this->assertEquals(['sort_one'], $params->getArray(QueryParams::DESC));
   }

   /**
    * @test
    */
   public function given_QueryParamsBuilder_when_withSearch()
   {
      // QueryString empty without resource
      $params = (new QueryParamsBuilder($this->request))->withSearch()->build();
      $this->assertFalse($params->has(QueryParams::SEARCH));

      // QueryString empty
      $params = (new QueryParamsBuilder($this->request, $this->resource))->withSearch()->build();
      $this->assertTrue($params->has(QueryParams::SEARCH));
      $this->assertEquals([], $params->getArray(QueryParams::SEARCH));

      // QueryString with one field to search
      $this->request->replace(['title' => 'title_value']);
      $params = (new QueryParamsBuilder($this->request, $this->resource))->withSearch()->build();
      $this->assertTrue($params->has(QueryParams::SEARCH));
      $this->assertEquals(['title' => 'title_value'], $params->getArray(QueryParams::SEARCH));

      // QueryString with two fields to search
      $this->request->replace(['name' => 'name_value', 'title' => 'title_value']);
      $params = (new QueryParamsBuilder($this->request, $this->resource))->withSearch()->build();
      $this->assertTrue($params->has(QueryParams::SEARCH));
      $this->assertEquals(['name' => 'name_value', 'title' => 'title_value'], $params->getArray(QueryParams::SEARCH));

      // QueryString complete and with two fields to search
      $this->request->replace([
         'limit' => 21,
         'skip' => 123,
         'field' => 'field_one',
         'include' => 'include_one',
         'sort' => 'sort_one',
         'desc' => 'desc_one',
         'name' => 'name_value',
         'title' => 'title_value'
      ]);
      $params = (new QueryParamsBuilder($this->request, $this->resource))->withSearch()->build();
      $this->assertTrue($params->has(QueryParams::SEARCH));
      $this->assertEquals(['name' => 'name_value', 'title' => 'title_value'], $params->getArray(QueryParams::SEARCH));
   }

   /**
    * @test
    */
   public function given_QueryParamsBuilder_when_withUseAsId()
   {
      // QueryString empty
      $params = (new QueryParamsBuilder($this->request))->withUseAsId(13)->build();
      $this->assertFalse($params->has(QueryParams::USE_AS_ID));
      $this->assertEquals([], $params->getArray(QueryParams::USE_AS_ID));

      // QueryString with use_as_id and int value
      $this->request->replace(['use_as_id' => 'one']);
      $params = (new QueryParamsBuilder($this->request))->withUseAsId(13)->build();
      $this->assertTrue($params->has(QueryParams::USE_AS_ID));
      $this->assertEquals(['one', 13], $params->getArray(QueryParams::USE_AS_ID));

      // QueryString with use_as_id and string value
      $this->request->replace(['use_as_id' => 'one']);
      $params = (new QueryParamsBuilder($this->request))->withUseAsId('myValue')->build();
      $this->assertTrue($params->has(QueryParams::USE_AS_ID));
      $this->assertEquals(['one', 'myValue'], $params->getArray(QueryParams::USE_AS_ID));

      // QueryString with int use_as_id
      $this->request->replace(['use_as_id' => 123]);
      $params = (new QueryParamsBuilder($this->request))->withUseAsId('myValue')->build();
      $this->assertTrue($params->has(QueryParams::USE_AS_ID));
      $this->assertEquals([123, 'myValue'], $params->getArray(QueryParams::USE_AS_ID));
   }

   /**
    * @test
    */
   public function given_QueryParamsBuilder_when_forFindCollection()
   {
      // QueryString empty
      $params = (new QueryParamsBuilder($this->request))->forFindCollection()->build();
      $this->assertTrue($params->has(QueryParams::LIMIT));
      $this->assertTrue($params->has(QueryParams::SKIP));
      $this->assertTrue($params->has(QueryParams::FIELD));
      $this->assertFalse($params->has(QueryParams::INCLUDE));
      $this->assertFalse($params->has(QueryParams::SORT));
      $this->assertFalse($params->has(QueryParams::DESC));
      $this->assertFalse($params->has(QueryParams::USE_AS_ID));
      $this->assertFalse($params->has(QueryParams::SEARCH));
      $this->assertEquals(0, $params->getInt(QueryParams::LIMIT));
      $this->assertEquals(0, $params->getInt(QueryParams::SKIP));
      $this->assertEquals(['*'], $params->getArray(QueryParams::FIELD));

      // QueryString complete with maximum parameters
      $this->request->replace(self::MAXIMUM_PARAMETERS);

      $params = (new QueryParamsBuilder($this->request))->forFindCollection()->build();
      $this->assertTrue($params->has(QueryParams::LIMIT));
      $this->assertTrue($params->has(QueryParams::SKIP));
      $this->assertTrue($params->has(QueryParams::FIELD));
      $this->assertTrue($params->has(QueryParams::INCLUDE));
      $this->assertTrue($params->has(QueryParams::SORT));
      $this->assertTrue($params->has(QueryParams::DESC));
      $this->assertFalse($params->has(QueryParams::SEARCH));
      $this->assertFalse($params->has(QueryParams::USE_AS_ID));

      $this->assertEquals(21, $params->getInt(QueryParams::LIMIT));
      $this->assertEquals(123, $params->getInt(QueryParams::SKIP));
      $this->assertEquals(['field_one'], $params->getArray(QueryParams::FIELD));
      $this->assertEquals(['includeOne'], $params->getArray(QueryParams::INCLUDE));
      $this->assertEquals(['sort_one'], $params->getArray(QueryParams::SORT));
      $this->assertEquals(['desc_one'], $params->getArray(QueryParams::DESC));
   }

   /**
    * @test
    */
   public function given_QueryParamsBuilder_when_forSearchCollection()
   {
      // QueryString empty
      $params = (new QueryParamsBuilder($this->request))->forSearchCollection()->build();
      $this->assertTrue($params->has(QueryParams::LIMIT));
      $this->assertTrue($params->has(QueryParams::SKIP));
      $this->assertTrue($params->has(QueryParams::FIELD));
      $this->assertFalse($params->has(QueryParams::INCLUDE));
      $this->assertFalse($params->has(QueryParams::SORT));
      $this->assertFalse($params->has(QueryParams::DESC));
      $this->assertFalse($params->has(QueryParams::SEARCH));
      $this->assertFalse($params->has(QueryParams::USE_AS_ID));
      $this->assertEquals(0, $params->getInt(QueryParams::LIMIT));
      $this->assertEquals(0, $params->getInt(QueryParams::SKIP));
      $this->assertEquals(['*'], $params->getArray(QueryParams::FIELD));

      // QueryString complete with maximum parameters
      $this->request->replace(self::MAXIMUM_PARAMETERS);

      $params = (new QueryParamsBuilder($this->request, $this->resource))->forSearchCollection()->build();
      $this->assertTrue($params->has(QueryParams::LIMIT));
      $this->assertTrue($params->has(QueryParams::SKIP));
      $this->assertTrue($params->has(QueryParams::FIELD));
      $this->assertTrue($params->has(QueryParams::INCLUDE));
      $this->assertTrue($params->has(QueryParams::SORT));
      $this->assertTrue($params->has(QueryParams::DESC));
      $this->assertTrue($params->has(QueryParams::SEARCH));
      $this->assertFalse($params->has(QueryParams::USE_AS_ID));
      $this->assertEquals(21, $params->getInt(QueryParams::LIMIT));
      $this->assertEquals(123, $params->getInt(QueryParams::SKIP));
      $this->assertEquals(['field_one'], $params->getArray(QueryParams::FIELD));
      $this->assertEquals(['includeOne'], $params->getArray(QueryParams::INCLUDE));
      $this->assertEquals(['sort_one'], $params->getArray(QueryParams::SORT));
      $this->assertEquals(['desc_one'], $params->getArray(QueryParams::DESC));
      $this->assertEquals(['name' => 'name_value', 'title' => 'title_value'], $params->getArray(QueryParams::SEARCH));
   }

   /**
    * @test
    */
   public function given_QueryParamsBuilder_when_forFindItem()
   {
      // QueryString empty
      $params = (new QueryParamsBuilder($this->request))->forFindItem(7)->build();
      $this->assertFalse($params->has(QueryParams::LIMIT));
      $this->assertFalse($params->has(QueryParams::SKIP));
      $this->assertTrue($params->has(QueryParams::FIELD));
      $this->assertFalse($params->has(QueryParams::INCLUDE));
      $this->assertFalse($params->has(QueryParams::SORT));
      $this->assertFalse($params->has(QueryParams::DESC));
      $this->assertFalse($params->has(QueryParams::SEARCH));
      $this->assertFalse($params->has(QueryParams::USE_AS_ID));
      $this->assertEquals(['*'], $params->getArray(QueryParams::FIELD));

      // QueryString complete with maximum parameters
      $this->request->replace(self::MAXIMUM_PARAMETERS);

      $params = (new QueryParamsBuilder($this->request, $this->resource))->forFindItem(7)->build();
      $this->assertFalse($params->has(QueryParams::LIMIT));
      $this->assertFalse($params->has(QueryParams::SKIP));
      $this->assertTrue($params->has(QueryParams::FIELD));
      $this->assertTrue($params->has(QueryParams::INCLUDE));
      $this->assertFalse($params->has(QueryParams::SORT));
      $this->assertFalse($params->has(QueryParams::DESC));
      $this->assertFalse($params->has(QueryParams::SEARCH));
      $this->assertTrue($params->has(QueryParams::USE_AS_ID));

      $this->assertEquals(['field_one'], $params->getArray(QueryParams::FIELD));
      $this->assertEquals(['includeOne'], $params->getArray(QueryParams::INCLUDE));
      $this->assertEquals(['use_as_id_value', 7], $params->getArray(QueryParams::USE_AS_ID));
   }
}


class MyDefaultMaximumBuilder extends QueryParamsBuilder
{
   /**
    * Constructor.
    * @param Request $request
    * @param Resource $resource
    */
   public function __construct(Request $request, Resource $resource = null)
   {
      parent::__construct($request, $resource);
      $this->defaultLimit = 77;
      $this->maximumLimit = 500;
   }
}

class MyResource extends Resource
{

   /**
    * Constructor.
    */
   public function __construct()
   {
      parent::__construct();
      $this->entity = new MyEntity();
   }

   /**
    * Transformer for the current resource.
    *
    * @return \League\Fractal\TransformerAbstract
    */
   protected function transformer()
   {
      return null;
   }
}

class MyEntity extends Entity
{
   protected $fillable = ['id', 'name', 'title'];
}
