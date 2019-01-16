<?php


namespace Domain\Entity;


use BadMethodCallException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use UnexpectedValueException;

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
      // It's important to call the next method before the parent constructor 
      $this->pushAssociatedForeignKey();
      $this->buildDefaultVisible();

      parent::__construct($attributes);
   }

   /**
    * Return the associated entity names.
    * @return array
    */
   public function getAssociated() : array
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
    * Push the associated Foreign Keys to fillable and hidden attributes.
    */
   protected function pushAssociatedForeignKey() : void
   {
      $keys = $this->findAllAssociatedForeignKey();
      $t = array_merge($this->getFillable(), $keys);
      $this->fillable($t);

      $this->addHidden($keys);
   }


   /**
    * Build the default visible attributes.
    */
   protected function buildDefaultVisible() : void
   {
      if (count($this->visible) === 0) {
         $visible = array_diff($this->getFillable(), $this->getHidden());
         $visible[] = $this->getKeyName();
         if ($this->timestamps) {
            $visible[] = $this->getCreatedAtColumn();
            $visible[] = $this->getUpdatedAtColumn();
         }
         $this->setVisible($visible);
      }
   }

   /**
    * Find all foreign keys of the associated entities.
    * @return array The name of the foreign keys
    * @throws BadMethodCallException If an associated entity don't exist
    * @throws UnexpectedValueException If an associated entity is not implemented
    */
   public function findAllAssociatedForeignKey() : array
   {
      $ret = array();
      foreach ($this->getAssociated() as $val) {
         $ret[] = $this->findAssociatedForeignKey($val);
      }
      return $ret;
   }

   /**
    * Find the foreign key of an associated entity.
    * @param string $associated The associated entity
    * @return string The foreign key
    * @throws BadMethodCallException If an associated entity don't exist
    * @throws UnexpectedValueException If an associated entity is not implemented
    */
   public function findAssociatedForeignKey(string $associated) : string
   {
      $forKey = null;
      $belong = $this->$associated();
      switch (get_class($belong)) {
         case BelongsTo::class :
            $forKey = $belong->getForeignKey();
            break;
         default:
            $mess = sprintf('Class not found "%s" for this associated attribute "%s"', get_class($belong), $associated);
            throw new UnexpectedValueException($mess);
      }
      return $forKey;
   }

}
