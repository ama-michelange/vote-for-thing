<?php

namespace Domain\Query;


use Domain\Helper\ArrayMapImp;

class QueryParamsImp extends ArrayMapImp implements QueryParams
{

   /**
    * Return true if all fields are required.
    * @return bool
    */
   public function hasAllFields() : bool
   {
      if ($this->has(QueryParams::FIELD) && $this->getArray(QueryParams::FIELD)[0] === '*') {
         return true;
      }
      return false;
   }

   /**
    * Return true if a limit > 0 exist.
    * @return bool
    */
   public function hasLimit() : bool
   {
      if ($this->has(QueryParams::LIMIT) && $this->getInt(QueryParams::LIMIT) > 0) {
         return true;
      }
      return false;
   }

   /**
    * Return true if a search exist.
    * @return bool
    */
   public function hasSearch() : bool
   {
      if ($this->has(QueryParams::SEARCH)) {
         return true;
      }
      return false;
   }

   /**
    * Return true if a search exist and it's empty.
    * @return bool
    */
   public function hasEmptySearch() : bool
   {
      if ($this->has(QueryParams::SEARCH) && count($this->getArray(QueryParams::SEARCH)) === 0) {
         return true;
      }
      return false;
   }
}
