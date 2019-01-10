<?php

namespace Domain\Query;

interface Command
{
   /**
    * Entity domain instance.
    *
    * @return \Domain\Entity\Entity;
    */
   public function entity();

}





