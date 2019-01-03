<?php


namespace Domain\Entity;


use Illuminate\Database\Eloquent\Model;

abstract class Entity extends Model
{
   protected $associated = [];

   /**
    * Create a new Entity instance based on Eloquent model.
    *
    * @param  array $attributes
    */
   public function __construct(array $attributes = [])
   {
      parent::__construct($attributes);
      $this->buildDefaultVisible();
   }

   /**
    * Return the associated entity names.
    * @return array
    */
   public function getAssociated():array
   {
      return $this->associated;
   }

   /**
    * Set the associated entity names.
    * @param  array $associated
    * @return $this
    */
   public function setAssociated(array $associated)
   {
      $this->associated = $associated;
      return $this;
   }

   /**
    * Build the default visible attributes.
    */
   protected function buildDefaultVisible() : void
   {
      if (count($this->visible) === 0) {
         $visible = $this->getFillable();
         $visible[] = $this->getKeyName();
         if ($this->timestamps) {
            $visible[] = $this->getCreatedAtColumn();
            $visible[] = $this->getUpdatedAtColumn();
         }
         $this->setVisible($visible);
      }
   }

}
