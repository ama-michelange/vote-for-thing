<?php


namespace tests\Unit;
use Domain\Helper\ArrayMapImp;
use Tests\TestCase;

class ArrayMapImpTest extends TestCase
{
   /**
    * @var \Domain\Helper\ArrayMapImp
    */
   protected $arrayMap;

   protected function setUp()
   {
      parent::setUp();
      $this->arrayMap = new ArrayMapImp();
      $this->arrayMap->put('int', 123);
      $this->arrayMap->put('string', 'myString');
      $this->arrayMap->put('array', ['myArrayOne', 'myArrayTwo', 'myArrayThree']);
   }

   public function testHas()
   {
      $this->assertTrue($this->arrayMap->has('int'));
      $this->assertTrue($this->arrayMap->has('string'));
      $this->assertTrue($this->arrayMap->has('array'));

      $this->assertFalse($this->arrayMap->has('unknown'));
   }

   public function testGet()
   {
      $this->assertEquals(123, $this->arrayMap->get('int'));
      $this->assertEquals('myString', $this->arrayMap->get('string'));
      $this->assertEquals(['myArrayOne', 'myArrayTwo', 'myArrayThree'], $this->arrayMap->get('array'));

      $this->assertNull($this->arrayMap->get('unknown'));
   }

   public function testGetInt()
   {
      $this->assertEquals(123, $this->arrayMap->getInt('int'));
      $this->assertEquals(0, $this->arrayMap->getInt('string'));
      $this->assertEquals(0, $this->arrayMap->getInt('array'));

      $this->assertEquals(0, $this->arrayMap->getInt('unknown'));
   }

   public function testGetArray()
   {
      $this->assertEquals([], $this->arrayMap->getArray('int'));
      $this->assertEquals([], $this->arrayMap->getArray('string'));
      $this->assertEquals(['myArrayOne', 'myArrayTwo', 'myArrayThree'], $this->arrayMap->getArray('array'));

      $this->assertEquals([], $this->arrayMap->getArray('unknown'));
   }

   public function testToArray()
   {
      $myArray = [
         'int' => 123,
         'string' => 'myString',
         'array' => ['myArrayOne', 'myArrayTwo', 'myArrayThree']];
      $this->assertEquals($myArray, $this->arrayMap->toArray());
   }
}
