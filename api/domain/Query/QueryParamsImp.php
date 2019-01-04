<?php

namespace Domain\Query;


use Domain\Helper\ArrayMapImp;

class QueryParamsImp extends ArrayMapImp implements QueryParams
{

   /**
    * {@inheritdoc}
    */
   public function hasAllFields() : bool
   {
      if ($this->has(QueryParams::FIELD) && $this->getArray(QueryParams::FIELD)[0] === '*') {
         return true;
      }
      return false;
   }

   /**
    * {@inheritdoc}
    */
   public function hasLimit() : bool
   {
      if ($this->has(QueryParams::LIMIT) && $this->getInt(QueryParams::LIMIT) > 0) {
         return true;
      }
      return false;
   }

   /**
    * {@inheritdoc}
    */
   public function hasSearch() : bool
   {
      if ($this->has(QueryParams::SEARCH)) {
         return true;
      }
      return false;
   }

   /**
    * {@inheritdoc}
    */
   public function hasEmptySearch() : bool
   {
      if ($this->has(QueryParams::SEARCH) && count($this->getArray(QueryParams::SEARCH)) === 0) {
         return true;
      }
      return false;
   }
}
