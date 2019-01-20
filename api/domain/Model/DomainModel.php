<?php


namespace Domain\Model;

use ArrayObject;

class DomainModel extends ArrayObject implements DomModel
{
   /**
    * Constructor.
    * @param array|null $input The array to initialize this instance
    */
   public function __construct(array $input = null)
   {
      parent::__construct(array(), ArrayObject::ARRAY_AS_PROPS);
      if (is_array($input)) {
         $this->fromArray($input);
      }
   }

   /**
    * @inheritdoc
    */
   public function offsetExists($index) : bool
   {
      $ret = parent::offsetExists($index);
      if (!$ret) {
         $tab = explode('.', $index);
         if (count($tab) > 0) {
            $ret = parent::offsetExists($tab[0]);
            if ($ret) {
               $obj = parent::offsetGet($tab[0]);
               array_shift($tab);
               $ret = $obj->offsetExists(implode('.', $tab));
            }
         }
      }
      return $ret;
   }

   /**
    * @inheritdoc
    */
   public function fromArray(array $input) : void
   {
      if (isset($input)) {
         foreach ($input as $key => $value) {
            if (is_array($value)) {
               $obj = new DomainModel();
               $this[$key] = $obj;
               $obj->fromArray($value);
            } else {
               $this[$key] = $value;
            }
         }
      }
   }

   /**
    * @inheritdoc
    */
   public function toArray()
   {
      $ret = array();
      foreach ($this as $key => $value) {
         if (is_object($value)) {
            $ret[$key] = $value->toArray();
         } else {
            $ret[$key] = $value;
         }
      }
      return $ret;
   }
}
