<?php

namespace Domain\Query;

interface QueryEntity extends Query
{
   /**
    * Return the builder for the query.
    *
    * @return QueryEntityBuilder
    */
   public function builder() : QueryEntityBuilder;

}





