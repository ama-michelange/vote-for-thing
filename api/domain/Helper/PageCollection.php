<?php

namespace Domain\Helper;

use Illuminate\Support\Collection;

interface PageCollection
{
   /**
    * Get the count number of items in this page.
    *
    * @return int
    */
   public function count() : int;

   /**
    * Get the total number of items in the data store.
    *
    * @return int
    */
   public function total() : int;

   /**
    * Get the "index" of the first item being paginated.
    *
    * @return int
    */
   public function firstItem() : int;

   /**
    * Get the "index" of the last item being paginated.
    *
    * @return int
    */
   public function lastItem() : int;

   /**
    * Get the current page number.
    *
    * @return int
    */
   public function currentPage() : int;

   /**
    * Get the page number of the last available page.
    *
    * @return int
    */
   public function lastPage() : int;

   /**
    * Get the number of items per page.
    *
    * @return int
    */
   public function perPage() : int;
   
   /**
    * Determine if there is more items in the data store.
    *
    * @return bool
    */
   public function hasMorePages() : bool;

   /**
    * Determine if this page is the first.
    *
    * @return bool
    */
   public function onFirstPage() : bool;

   /**
    * Get all of the items being paginated.
    *
    * @return Collection
    */
   public function items() : Collection;

   /**
    * Determine if the list of items is empty or not.
    *
    * @return bool
    */
   public function isEmpty() : bool;

}
