<?php


namespace Tests\Unit;


use ArrayObject;
use Tests\TestCase;

class ThingArrayObjectTest extends TestCase
{

   /**
    * @test
    */
   public function study_ArrayObject()
   {
      $data = [
//         'data' => [
         'title' => 'Sally',
         'proper_title' => 'Molly',
         'category' => [
            'data' => [
               'id' => 1
            ]
         ]
//         ]
      ];

      $obj = new ArrayObject($data, ArrayObject::ARRAY_AS_PROPS);
//      var_dump($obj);
      $this->assertEquals('Sally', $obj->title);
      $this->assertEquals('Molly', $obj->proper_title);
      $this->assertEquals('Sally', $obj['title']);
      $this->assertEquals('Molly', $obj['proper_title']);
      $this->assertEquals('Sally', $obj->offsetGet('title'));
      $this->assertEquals('Molly', $obj->offsetGet('proper_title'));
      $this->assertTrue($obj->offsetExists('title'));
      $this->assertTrue($obj->offsetExists('proper_title'));
      $this->assertTrue($obj->offsetExists('category'));
      $this->assertFalse($obj->offsetExists('category.data'));

      $obj->title = 'ama';
      $this->assertEquals('ama', $obj->title);
      $obj['title'] = 'amadeus';
      $this->assertEquals('amadeus', $obj->title);

//      foreach ($obj as $key => $value) {
//         echo "k:$key, typev:" . gettype($value) . "\n";
//      }
   }


   /**
    * @test
    */
   public function study_MyArrayObject()
   {
      $data = [
         'title' => 'Sally',
         'proper_title' => 'Molly',
         'category' => [
            'id' => 1
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

      $obj = new MyArrayObject();
      $obj->import($data);
//      var_dump($obj);

      $this->assertEquals('Sally', $obj->title);
      $this->assertEquals('Molly', $obj->proper_title);
      $this->assertEquals('Sally', $obj['title']);
      $this->assertEquals('Molly', $obj['proper_title']);
      $this->assertEquals('Sally', $obj->offsetGet('title'));
      $this->assertEquals('Molly', $obj->offsetGet('proper_title'));
      $this->assertTrue($obj->offsetExists('title'));
      $this->assertTrue($obj->offsetExists('proper_title'));

      $this->assertTrue($obj->offsetExists('category'));
      $this->assertTrue($obj->offsetExists('category.id'));
      $this->assertEquals(1, $obj->category->id);


      $this->assertTrue($obj->offsetExists('authors'));
      $this->assertTrue($obj->offsetExists('authors.0'));
      $this->assertTrue($obj->offsetExists('authors.0.person'));
      $this->assertTrue($obj->offsetExists('authors.0.person.id'));
      $this->assertTrue($obj->offsetExists('authors.0.person.fname'));
      $this->assertTrue($obj->offsetExists('authors.0.person.lname'));
      $this->assertEquals(123, $obj->authors[0]->person->id);
      $this->assertEquals('toto', $obj->authors[0]->person->fname);
      $this->assertEquals('leboeuf', $obj->authors[0]->person->lname);

      $this->assertTrue($obj->offsetExists('authors.1'));
      $this->assertTrue($obj->offsetExists('authors.1.person'));
      $this->assertTrue($obj->offsetExists('authors.1.person.id'));
      $this->assertFalse($obj->offsetExists('authors.1.person.fname'));
      $this->assertTrue($obj->offsetExists('authors.1.person.lname'));
      $this->assertEquals(97, $obj->authors[1]->person->id);
      $this->assertEquals('moebius', $obj->authors[1]->person->lname);


      $obj->title = 'ama';
      $this->assertEquals('ama', $obj->title);
      $obj['title'] = 'amadeus';
      $this->assertEquals('amadeus', $obj->title);

      foreach ($obj as $key => $value) {
         echo "k:$key, typev:" . gettype($value) . "\n";
      }
   }
}

class MyArrayObject extends ArrayObject
{

   /**
    * Constructor.
    */
   public function __construct()
   {
      parent::__construct(array(), ArrayObject::ARRAY_AS_PROPS);
   }

   public function offsetExists($index) : bool
   {
      $ret = parent::offsetExists($index);
      if (!$ret) {
         $tab = explode('.', $index);
         if (count($tab) > 0) {
            $ret = parent::offsetExists($tab[0]);
            if ($ret) {
               $obj = $this->offsetGet($tab[0]);
               array_shift($tab);
               $ret = $obj->offsetExists(implode('.', $tab));
            }
         }
      }
      return $ret;
   }

   public
   function import(array $input) : void
   {
      if (isset($input)) {
         foreach ($input as $key => $value) {
            if (is_array($value)) {
               $obj = new MyArrayObject();
               $this[$key] = $obj;
               $obj->import($value);
            } else {
               $this[$key] = $value;
            }
         }
      }
   }
}
