<?php

namespace Domain\Query;

use Illuminate\Support\Collection;

interface Query
{
   /**
    * Entity domain instance.
    *
    * @return \Domain\Entity\Entity;
    */
   public function entity();


//   /**
//    * Return the builder for the query.
//    *
//    * @return QueryEntityBuilder
//    */
//   public function builder() : QueryEntityBuilder;

   /**
    * Find a collection of entities objects based on query parameters.
    *
    * @param QueryParams $queryParams
    * @return Collection
    */
   public function findCollection(QueryParams $queryParams) : Collection;

}





