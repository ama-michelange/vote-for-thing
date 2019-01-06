<?php

namespace Domain\Helper;

interface ArrayMap
{
   /**
    * @param $pName
    * @return bool
    */
   public function has($pName) : bool;

   /**
    * @param $pName
    * @return mixed
    */
   public function get($pName);

   /**
    * @param $pName
    * @return array
    */
   public function getArray($pName) : array;

   /**
    * @param $pName
    * @return int
    */
   public function getInt($pName) : int;

   /**
    * @param $pName
    * @return string
    */
   public function getString($pName) : string;

   /**
    * @return array
    */
   public function toArray() : array;
}
