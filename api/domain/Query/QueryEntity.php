<?php

namespace Domain\Query;

interface QueryEntity extends Query
{
   /**
    * Return the builder of the query.
    *
    * @return QueryEntityBuilder The builder used by the query
    */
   public function builder() : QueryEntityBuilder;

}





