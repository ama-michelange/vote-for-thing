<?php

namespace Domain\Query;

use Domain\Entity\Entity;
use Illuminate\Support\Collection;

interface Query
{
   /**
    * Entity domain instance.
    *
    * @return \Domain\Entity\Entity;
    */
   public function entity();

   /**
    * Find a collection of entities objects based on query parameters.
    *
    * @param QueryParams $queryParams The parameters of the query
    * @return Collection A collection of entity
    */
   public function findCollection(QueryParams $queryParams) : Collection;

   /**
    * Find a item of entities objects based on query parameters.
    * 
    * @param string|int $id Identifier of the item to find
    * @param QueryParams $queryParams The parameters of the query
    * @return Entity|null The requested object or null if not found
    */
   public function findItem($id, QueryParams $queryParams) : Entity;

}





