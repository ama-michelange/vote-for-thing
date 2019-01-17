<?php

namespace Domain\Command;

interface Command
{
   /**
    * Entity domain instance.
    *
    * @return \Domain\Entity\Entity;
    */
   public function entity();

}





