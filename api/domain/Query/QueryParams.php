<?php


namespace Domain\Query;


use Domain\Helper\ArrayMap;

interface QueryParams extends ArrayMap
{
   const FIELD = 'field';
   const INCLUDE = 'include';
   const LIMIT = 'limit';
   const SKIP = 'skip';
   const SORT = 'sort';
   const DESC = 'desc';
   const SEARCH = 'search';
   const USE_AS_ID = 'use_as_id';

   /**
    * Return true if all fields are required.
    * @return bool
    */
   public function hasAllFields():bool;

   /**
    * Return true if a limit > 0 exist.
    * @return bool
    */
   public function hasLimit():bool;

   /**
    * Return true if a search exist.
    * @return bool
    */
   public function hasSearch() : bool;

   /**
    * Return true if a search exist and it's empty.
    * @return bool
    */
   public function hasEmptySearch() : bool;

   /**
    * Return true if a key exists to use as ID.
    * @return bool
    */
   public function hasUseAsId() : bool;
}
