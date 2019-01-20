<?php


namespace Domain\Model;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;

interface DomModel extends ArrayAccess, Arrayable
{
   /**
    * Initialize the model with a array.
    * @param array $input An array to initialize the model
    */
   public function fromArray(array $input) : void;
}
