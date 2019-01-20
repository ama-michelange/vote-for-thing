<?php


namespace Tests\Unit\Domain;


use Domain\Model\DomainModel;
use Tests\TestCase;

class ModelTest extends TestCase
{
   const DATA_SIMPLE_TYPE = [
      'string_sally' => 'Sally',
      'string_molly' => 'Molly',
      'int_0' => 0,
      'int_123' => 123,
      'float_0_0' => 0.0,
      'float_123_456' => 123.456,
      'bool_true' => true,
      'bool_false' => false,
   ];

   const DATA_INCLUDES = [
      'title' => 'Sally',
      'proper_title' => 'Molly',
      'category' => [
         'id' => 1,
         'name' => 'book'
      ],
      'authors' => [
         0 => [
            'person' => [
               'id' => 123,
               'lname' => 'leboeuf',
               'fname' => 'toto'
            ]
         ],
         1 => [
            'person' => [
               'id' => 97,
               'lname' => 'moebius'
            ]
         ]
      ]
   ];
   
   /**
    * @test
    */
   public function given_DomainModel_when_new_without_param_then_array_is_empty()
   {
      $obj = new DomainModel();
      $this->assertEquals([], $obj->toArray());
      $this->assertEquals(0, $obj->count());
   }

   /**
    * @test
    */
   public function given_DomainModel_when_new_with_DataSimpleType_then_all_datas_are_got()
   {
      $obj = new DomainModel(ModelTest::DATA_SIMPLE_TYPE);
      $this->assertEquals(ModelTest::DATA_SIMPLE_TYPE, $obj->toArray());
      $this->assertEquals(8, $obj->count());

      $this->assertEquals('Sally', $obj->string_sally);
      $this->assertEquals('Molly', $obj->string_molly);
      $this->assertEquals(0, $obj->int_0);
      $this->assertEquals(123, $obj->int_123);
      $this->assertEquals(0.0, $obj->float_0_0);
      $this->assertEquals(123.456, $obj->float_123_456);
      $this->assertEquals(true, $obj->bool_true);
      $this->assertEquals(false, $obj->bool_false);

      $this->assertEquals('Sally', $obj['string_sally']);
      $this->assertEquals('Molly', $obj['string_molly']);
      $this->assertEquals(0, $obj['int_0']);
      $this->assertEquals(123, $obj['int_123']);
      $this->assertEquals(0.0, $obj['float_0_0']);
      $this->assertEquals(123.456, $obj['float_123_456']);
      $this->assertEquals(true, $obj['bool_true']);
      $this->assertEquals(false, $obj['bool_false']);

   }

   /**
    * @test
    */
   public function given_DomainModel_when_new_with_DataIncludes_then_all_datas_are_got()
   {
      $obj = new DomainModel(ModelTest::DATA_INCLUDES);
      $this->assertEquals(ModelTest::DATA_INCLUDES, $obj->toArray());
      $this->assertEquals(4, $obj->count());

      $this->assertEquals('Sally', $obj->title);
      $this->assertEquals('Molly', $obj->proper_title);

      $this->assertEquals(ModelTest::DATA_INCLUDES['category'], $obj->category->toArray());
      $this->assertEquals(1, $obj->category->id);
      $this->assertEquals('book', $obj->category->name);
      $this->assertEquals(123, $obj->authors[0]->person->id);
      $this->assertEquals('toto', $obj->authors[0]->person->fname);
      $this->assertEquals('leboeuf', $obj->authors[0]->person->lname);
      $this->assertEquals(97, $obj->authors[1]->person->id);
      $this->assertEquals('moebius', $obj->authors[1]->person->lname);

      $this->assertEquals(1, $obj['category']['id']);
      $this->assertEquals('book', $obj['category']['name']);

      $this->assertEquals(123, $obj['authors'][0]['person']['id']);
      $this->assertEquals('toto', $obj['authors'][0]['person']['fname']);
      $this->assertEquals('leboeuf', $obj['authors'][0]['person']['lname']);
      $this->assertEquals(97, $obj['authors'][1]['person']['id']);
      $this->assertEquals('moebius', $obj['authors'][1]['person']['lname']);
   }

   /**
    * @test
    */
   public function given_DomainModel_when_new_with_DataIncludes_then_all_includes_are_Model()
   {
      $obj = new DomainModel(ModelTest::DATA_INCLUDES);
      $this->assertEquals(4, $obj->count());

      $this->assertEquals('Domain\Model\DomainModel', get_class($obj->category));
      $this->assertEquals('Domain\Model\DomainModel', get_class($obj->authors));
      $this->assertEquals('Domain\Model\DomainModel', get_class($obj->authors[0]));
      $this->assertEquals('Domain\Model\DomainModel', get_class($obj->authors[0]->person));
      $this->assertEquals('Domain\Model\DomainModel', get_class($obj->authors[1]));
      $this->assertEquals('Domain\Model\DomainModel', get_class($obj->authors[1]->person));
   }

   /**
    * @test
    */
   public function given_DomainModel_when_new_with_DataIncludes_then_all_includes_has_good_count()
   {
      $obj = new DomainModel(ModelTest::DATA_INCLUDES);
      $this->assertEquals(4, $obj->count());

      $this->assertEquals(2, $obj->category->count());
      $this->assertEquals(2, $obj->authors->count());
      $this->assertEquals(1, $obj->authors[0]->count());
      $this->assertEquals(3, $obj->authors[0]->person->count());
      $this->assertEquals(1, $obj->authors[1]->count());
      $this->assertEquals(2, $obj->authors[1]->person->count());
   }

   /**
    * @test
    */
   public function given_DomainModel_when_use_offsetExists_with_include_or_not_then_all_returns_are_exact()
   {
      $obj = new DomainModel(ModelTest::DATA_INCLUDES);
      $this->assertTrue($obj->offsetExists('title'));
      $this->assertTrue($obj->offsetExists('proper_title'));

      $this->assertTrue($obj->offsetExists('category'));
      $this->assertTrue($obj->offsetExists('category.id'));
      $this->assertTrue($obj->offsetExists('category.name'));
      $this->assertFalse($obj->offsetExists('category.unknown'));

      $this->assertTrue($obj->offsetExists('authors'));
      $this->assertTrue($obj->offsetExists('authors.0'));
      $this->assertTrue($obj->offsetExists('authors.0.person'));
      $this->assertTrue($obj->offsetExists('authors.0.person.id'));
      $this->assertTrue($obj->offsetExists('authors.0.person.fname'));
      $this->assertTrue($obj->offsetExists('authors.0.person.lname'));

      $this->assertTrue($obj->offsetExists('authors.1'));
      $this->assertTrue($obj->offsetExists('authors.1.person'));
      $this->assertTrue($obj->offsetExists('authors.1.person.id'));
      $this->assertFalse($obj->offsetExists('authors.1.person.fname'));
      $this->assertTrue($obj->offsetExists('authors.1.person.lname'));
   }
}
