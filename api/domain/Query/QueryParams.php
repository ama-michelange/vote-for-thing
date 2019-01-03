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
}
