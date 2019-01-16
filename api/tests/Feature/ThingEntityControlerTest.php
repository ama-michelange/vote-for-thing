<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Log;
use Tests\DatabaseMigrateTestCase;

class ThingEntityControlerTest extends DatabaseMigrateTestCase
{

   /**
    * @test
    */
   public function getAll()
   {
      $response = $this->json('GET', 'api/qthings');
      $response->assertStatus(200);
//      Log::debug(print_r($response, true));
//      Log::debug(print_r($response->getOriginalContent(), true));
      $response->assertJson([
         'data' => []
      ]);
      $response->assertJsonStructure([
         'data' => [
            [
               'id',
               'title',
               'lib_title',
               'proper_title',
               'number',
               'legal',
               'image_url',
               'description_url',
               'created_at',
               'updated_at'
            ]
         ]
      ]);
      $a = $response->getOriginalContent();
      $this->assertCount(1, $a);
      $this->assertTrue(count($a['data']) > 200);
   }

   /**
    * @test
    */
   public function getAll_With_Field()
   {
      $response = $this->json('GET', 'api/qthings?field=id,title,number,proper_title');
      $response->assertStatus(206);
      $response->assertJson([
         'data' => []
      ]);
      $response->assertJsonStructure([
         'data' => [
            [
               'id',
               'title',
               'proper_title',
               'number'
            ]
         ]
      ]);
      $a = $response->getOriginalContent();
      $this->assertCount(1, $a);
      $this->assertTrue(count($a['data']) > 200);
   }

   /**
    * @test
    */
   public function getAll_With_Unknown_Field()
   {
      $response = $this->json('GET', 'api/qthings?field=id,toto,proper_title,numero');
//      Log::debug(print_r($response, true));
      $response->assertStatus(400);
      $response->assertJson([
         'message' => '[Bad Request] Unknown field : toto,numero',
         'status_code' => 400
      ]);
   }

   /**
    * @test
    */
   public function getAll_With_Include()
   {
      $response = $this->json('GET', 'api/qthings?include=category');
      $response->assertStatus(200);
//      Log::debug(print_r($response, true));
//      Log::debug(print_r($response->getOriginalContent(), true));
      $response->assertJson([
         'data' => []
      ]);
      $response->assertJsonStructure([
         'data' => [
            [
               'id',
               'title',
               'lib_title',
               'proper_title',
               'number',
               'legal',
               'image_url',
               'description_url',
               'created_at',
               'updated_at',
               'category' => [
                  'data' => [
                     'id',
                     'name',
                     'created_at',
                     'updated_at'
                  ]
               ]
            ]
         ]
      ]);
      $a = $response->getOriginalContent();
      $this->assertCount(1, $a);
      $this->assertTrue(count($a['data']) > 200);
      $this->assertEquals('comic', $a['data'][0]['category']['data']['name']);
   }

   /**
    * @test
    */
   public function getAll_With_Include_Unknown()
   {
      $response = $this->json('GET', 'api/qthings?include=id,toto,category,numero');
//      Log::debug(print_r($response, true));
      $response->assertStatus(400);
      $response->assertJson([
         'message' => '[Bad Request] Unknown object to include : id,toto,numero',
         'status_code' => 400
      ]);
   }

   /**
    * @test
    */
   public function getAll_With_Limit()
   {
      $response = $this->json('GET', 'api/qthings?limit=5');
      $response->assertStatus(200);

      $a = $response->getOriginalContent();
      $this->assertCount(2, $a);
      $this->assertEquals(5, count($a['data']));

//      Log::debug(print_r($response->getOriginalContent(), true));

      $response->assertJson([
         'meta' => [
            'cursor' => [
               'current' => 0,
               'prev' => 0,
               'next' => 5,
               'count' => 5
            ]
         ],
      ]);
   }

   /**
    * @test
    */
   public function getAll_With_Limit_Skip()
   {
      $response = $this->json('GET', 'api/qthings?limit=5&skip=7');
      $response->assertStatus(200);

      $a = $response->getOriginalContent();
      $this->assertCount(2, $a);
      $this->assertEquals(5, count($a['data']));

//      Log::debug(print_r($response->getOriginalContent(), true));

      $response->assertJson([
         'meta' => [
            'cursor' => [
               'current' => 7,
               'prev' => 2,
               'next' => 7,
               'count' => 5
            ]
         ],
      ]);
   }

   /**
    * @test
    */
   public function getAll_With_Limit_Skip_BadStringValue()
   {
      $response = $this->json('GET', 'api/qthings?limit=foo&skip=toto');
      $response->assertStatus(200);

      $a = $response->getOriginalContent();
      $this->assertCount(1, $a);
      $this->assertTrue(count($a['data']) > 200);

//      Log::debug(print_r($response->getOriginalContent(), true));

      $response->assertJsonMissing([
         'meta' => [
            'cursor' => []
         ],
      ]);
   }

   /**
    * @test
    */
   public function getAll_With_Sort_1()
   {

      $response = $this->json('GET', 'api/qthings?sort=lib_title,number,proper_title');
      $response->assertStatus(200);

      $a = $response->getOriginalContent();
      $this->assertCount(1, $a);
      $this->assertTrue(count($a['data']) > 200);

//      Log::debug(print_r($a['data'][0], true));
//      Log::debug(print_r($response->getOriginalContent(), true));

      $this->assertEquals('Les 3 Fruits', $a['data'][0]['title']);
      $this->assertEquals('3 secondes', $a['data'][1]['title']);
      $this->assertEquals('A Silent Voice', $a['data'][2]['title']);
      $this->assertEquals('A coucher dehors', $a['data'][3]['title']);
      $this->assertEquals('A.D. After Death', $a['data'][4]['title']);

   }

   /**
    * @test
    */
   public function getAll_With_Sort_2()
   {
      $response = $this->json('GET', 'api/qthings?sort=number,lib_title,proper_title');
      $response->assertStatus(200);

      $a = $response->getOriginalContent();
      $this->assertCount(1, $a);
      $this->assertTrue(count($a['data']) > 200);

//      Log::debug(print_r($a['data'][0], true));
//      Log::debug(print_r($response->getOriginalContent(), true));

      $this->assertEquals('Les 3 Fruits', $a['data'][0]['title']);
      $this->assertEquals('3 secondes', $a['data'][1]['title']);
      $this->assertEquals('A coucher dehors', $a['data'][2]['title']);
      $this->assertEquals('A.D. After Death', $a['data'][3]['title']);
      $this->assertEquals('Ailefroide Altitude 3954', $a['data'][4]['title']);
   }

   /**
    * @test
    */
   public function getAll_With_Sort_And_Unknown_Field()
   {
      $response = $this->json('GET', 'api/qthings?sort=toto_title,number,proper_title,foo');
//      Log::debug(print_r($response, true));
      $response->assertStatus(400);
      $response->assertJson([
         'message' => '[Bad Request] Unknown field to sort : toto_title,foo',
         'status_code' => 400
      ]);
   }

   /**
    * @test
    */
   public function getAll_With_Sort_And_Desc_1()
   {
      $response = $this->json('GET', 'api/qthings?desc&sort=number,lib_title,proper_title');
      $response->assertStatus(200);

      $a = $response->getOriginalContent();
      $this->assertCount(1, $a);
      $this->assertTrue(count($a['data']) > 200);

//      Log::debug(print_r($a['data'][0], true));
//      Log::debug(print_r($response->getOriginalContent(), true));

      $this->assertEquals(6, $a['data'][0]['number']);
      $this->assertEquals('Horologiom', $a['data'][0]['title']);

      $this->assertEquals(4, $a['data'][1]['number']);
      $this->assertEquals('Alter Ego', $a['data'][1]['title']);

      $this->assertEquals(3, $a['data'][2]['number']);
      $this->assertEquals('Le casse', $a['data'][2]['title']);
   }

   /**
    * @test
    */
   public function getAll_With_Sort_And_Desc_2()
   {
      $response = $this->json('GET', 'api/qthings?desc=number,lib_title&sort=number,lib_title');
      $response->assertStatus(200);

      $a = $response->getOriginalContent();
      $this->assertCount(1, $a);
      $this->assertTrue(count($a['data']) > 200);

//      Log::debug(print_r($a['data'][0], true));
//      Log::debug(print_r($response->getOriginalContent(), true));

      $this->assertEquals(6, $a['data'][0]['number']);
      $this->assertEquals('Horologiom', $a['data'][0]['title']);

      $this->assertEquals(4, $a['data'][1]['number']);
      $this->assertEquals('Alter Ego', $a['data'][1]['title']);

      $this->assertEquals(3, $a['data'][2]['number']);
      $this->assertEquals('Les quatre de Baker Street', $a['data'][2]['title']);
   }

   /**
    * @test
    */
   public function getAll_With_Sort_And_Desc_And_Unknown_Field()
   {
      $response = $this->json('GET', 'api/qthings?sort=lib_title&desc=toto_title,number,proper_title,foo');
//      Log::debug(print_r($response, true));
      $response->assertStatus(400);
      $response->assertJson([
         'message' => '[Bad Request] Unknown field to descendant sort : toto_title,foo',
         'status_code' => 400
      ]);
   }

   /**
    * @test
    */
   public function getSearch_With_Like_Begin()
   {
      $response = $this->json('GET', 'api/qthings/search?lib_title=hor*');
      $response->assertStatus(200);

//      Log::debug(print_r($response->getOriginalContent(), true));

      $a = $response->getOriginalContent();
      $this->assertCount(1, $a);
      $this->assertTrue(count($a['data']) === 2);

      $this->assertEquals('Horologiom', $a['data'][0]['lib_title']);
      $this->assertEquals('Horde du Contrevent (La)', $a['data'][1]['lib_title']);
   }

   /**
    * @test
    */
   public function getSearch_With_Like_Contains()
   {
      $response = $this->json('GET', 'api/qthings/search?lib_title=*hor*');
      $response->assertStatus(200);

      Log::debug(print_r($response->getOriginalContent(), true));

      $a = $response->getOriginalContent();
      $this->assertCount(1, $a);
      $this->assertTrue(count($a['data']) === 4);

//      Log::debug(print_r($a['data'][0], true));

      $this->assertEquals('Horologiom', $a['data'][0]['lib_title']);
      $this->assertEquals('A coucher dehors', $a['data'][1]['lib_title']);
      $this->assertEquals('Collaboration Horizontale', $a['data'][2]['lib_title']);
      $this->assertEquals('Horde du Contrevent (La)', $a['data'][3]['lib_title']);
   }

   /**
    * @test
    */
   public function getSearch_Without_Parameters()
   {
      $response = $this->json('GET', 'api/qthings/search');
      $response->assertStatus(400);
      $response->assertJson([
         'message' => '[Bad Request] No field to search',
         'status_code' => 400
      ]);
   }

   /**
    * @test
    */
   public function getId()
   {
      $response = $this->json('GET', 'api/qthings/10');
      $response->assertStatus(200);
      $response->assertJson([
         'data' => [
            'id' => 10
         ]
      ]);
      $response->assertJsonStructure([
         'data' => [
            'id',
            'title',
            'lib_title',
            'proper_title',
            'number',
            'legal',
            'image_url',
            'description_url',
            'created_at',
            'updated_at'
         ]
      ]);
   }

   /**
    * @test
    */
   public function getId_With_Field()
   {
      $response = $this->json('GET', 'api/qthings/10?field=id,title');
      $response->assertStatus(206);
      $response->assertJson([
         'data' => [
            'id' => 10,
            'title' => "La mémoire de l'eau"
         ]
      ]);
      $response->assertJsonStructure([
         'data' => [
            'id',
            'title'
         ]
      ]);
   }

   /**
    * @test
    */
   public function getId_With_Include()
   {
      $response = $this->json('GET', 'api/qthings/10?include=category');
      $response->assertStatus(200);
      $response->assertJson([
         'data' => [
            'id' => 10,
            'title' => "La mémoire de l'eau",
            'category' => [
               'data' => [
                  'id' => 1,
                  'name' => 'comic'
               ]
            ]
         ]
      ]);
      $response->assertJsonStructure([
         'data' => [
            'id',
            'title',
            'lib_title',
            'proper_title',
            'number',
            'legal',
            'image_url',
            'description_url',
            'created_at',
            'updated_at',
            'category' => [
               'data' => [
                  'id',
                  'name',
                  'created_at',
                  'updated_at'
               ]
            ]
         ]
      ]);
   }

   /**
    * @test
    */
   public function getId_With_UseAsId()
   {
      $response = $this->json('GET', 'api/qthings/10?use_as_id=id');
      $response->assertStatus(200);
      $response->assertJson([
         'data' => [
            'id' => 10
         ]
      ]);
      $response->assertJsonStructure([
         'data' => [
            'id',
            'title',
            'lib_title',
            'proper_title',
            'number',
            'legal',
            'image_url',
            'description_url',
            'created_at',
            'updated_at'
         ]
      ]);
   }

   /**
    * @test
    */
   public function create()
   {
      $this->markTestIncomplete(
         'This test has not been implemented yet.'
      );
      $data = [
         'data' => [
            'title' => 'Sally',
            'category' => [
               'data' => [
                  'id' => 1
               ]
            ]
         ]
      ];
      $response = $this->json('POST', 'api/qthings', $data);

      Log::debug(print_r($response, true));

      $response
         ->assertStatus(201)
         ->assertJson([
            'created' => true,
         ]);
   }

   /**
    * @test
    */
   public function create_When_Empty_Data()
   {
      $response = $this->json('POST', 'api/qthings');
      $response->assertStatus(400)
         ->assertJson([
            "message" => "Empty data",
            "status_code" => 400,
         ]);
      $response = $this->json('POST', 'api/qthings', []);
      $response->assertStatus(400);
      $response = $this->json('POST', 'api/qthings', ['name' => 'Sally']);
      $response->assertStatus(400);
   }
}
