<?php

namespace Tests\Feature;

use Tests\DatabaseMigrateTestCase;

class ThingQueryControlerTest extends DatabaseMigrateTestCase
{

   /**
    * Find all things.
    *
    * @return void
    */
   public function testGetAll()
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

   public function testGetAllWithField()
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

   public function testGetAllWithField_Unknown()
   {
      $response = $this->json('GET', 'api/qthings?field=id,toto,proper_title,numero');
//      Log::debug(print_r($response, true));
      $response->assertStatus(400);
      $response->assertJson([
         'message' => '[Bad request] Unknown field : toto,numero',
         'status_code' => 400
      ]);
   }


   public function testGetInclude()
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

   public function testGetAllWithInclude_Unknown()
   {
      $response = $this->json('GET', 'api/qthings?include=id,toto,category,numero');
//      Log::debug(print_r($response, true));
      $response->assertStatus(400);
      $response->assertJson([
         'message' => '[Bad request] Unknown object to include : id,toto,numero',
         'status_code' => 400
      ]);
   }

   public function testGetAllWithLimit()
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

   public function testGetAllWithLimitSkip()
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


   public function testGetAllWithLimitSkip_BadStringValue()
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

   public function testGetAllWithSort_1()
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

   public function testGetAllWithSort_2()
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

   public function testGetAllWithSort_UnknownField()
   {
      $response = $this->json('GET', 'api/qthings?sort=toto_title,number,proper_title,foo');
//      Log::debug(print_r($response, true));
      $response->assertStatus(400);
      $response->assertJson([
         'message' => '[Bad request] Unknown field to sort : toto_title,foo',
         'status_code' => 400
      ]);
   }

   public function testGetAllWithSortDesc_1()
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

   public function testGetAllWithSortDesc_2()
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


   public function testGetAllWithSortDesc_UnknownField()
   {
      $response = $this->json('GET', 'api/qthings?sort=lib_title&desc=toto_title,number,proper_title,foo');
//      Log::debug(print_r($response, true));
      $response->assertStatus(400);
      $response->assertJson([
         'message' => '[Bad request] Unknown field to descendant sort : toto_title,foo',
         'status_code' => 400
      ]);
   }

   public function testOne()
   {
      $this->markTestIncomplete(
         'This test has not been implemented yet.'
      );
      $response = $this->json('GET', 'api/qthings/10');
      $response->assertStatus(200);
//      Log::debug(print_r($response, true));
//      Log::debug(print_r($response->getOriginalContent(), true));
      $response->assertJson([
         'data' => []
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

   public function testOneField()
   {
      $this->markTestIncomplete(
         'This test has not been implemented yet.'
      );

      $response = $this->json('GET', 'api/qthings/10?field=id,title');
//     Log::debug(print_r($response, true));
      $response->assertStatus(206);
//      Log::debug(print_r($response->getOriginalContent(), true));
      $response->assertJson([
         'data' => [],
         'meta' => [],
      ]);
      $response->assertJsonStructure([
         'data' => [
            'id',
            'title'
         ],
         'meta' => [],
      ]);
   }

}
