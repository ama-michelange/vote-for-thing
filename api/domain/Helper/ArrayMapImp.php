<?php

namespace Domain\Helper;


class ArrayMapImp implements ArrayMap
{
   protected $map;

   /**
    * Constructor.
    */
   public function __construct()
   {
      $this->map = array();
   }

   /**
    * {@inheritdoc}
    */
   public function has($pName) : bool
   {
      if (array_key_exists($pName, $this->map)) {
         $value = $this->map[$pName];
         if (isset($value)) {
            return true;
         }
      }
      return false;
   }

   /**
    * {@inheritdoc}
    */
   public function get($pName)
   {
      if (array_key_exists($pName, $this->map)) {
         return $this->map[$pName];
      }
      return null;
   }

   /**
    * {@inheritdoc}
    */
   public function getArray($pName) : array
   {
      if (array_key_exists($pName, $this->map)) {
         $value = $this->map[$pName];
         if (is_array($value)) {
            return $value;
         }
      }
      return [];
   }

   /**
    * {@inheritdoc}
    */
   public function getInt($pName) : int
   {
      if (array_key_exists($pName, $this->map)) {
         $value = $this->map[$pName];
         if (is_int($value)) {
            return $value;
         }
      }
      return 0;
   }

   /**
    * {@inheritdoc}
    */
   public function getString($pName) : string
   {
      if (array_key_exists($pName, $this->map)) {
         $value = $this->map[$pName];
         if (is_string($value)) {
            return $value;
         }
         if (is_int($value)) {
            return strval($value);
         }
      }
      return '';
   }

   /**
    * {@inheritdoc}
    */
   public function toArray() : array
   {
      return array_merge([], $this->map);
   }

   /**
    * {@inheritdoc}
    */
   public function put($pName, $pValue) : void
   {
      $this->map[$pName] = $pValue;
   }
}
