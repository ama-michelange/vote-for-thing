<?php
/**
 * Created by IntelliJ IDEA.
 * User: a454895
 * Date: 22/12/2018
 * Time: 02:36
 */
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
    * @return array
    */
   public function toArray() : array;
}
