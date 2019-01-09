<?php

namespace Domain\Helper;

use Illuminate\Support\Collection;

class PaginatorCollection implements PageCollection
{
   protected $count = 0;
   protected $total = 0;
   protected $firstItem = 0;
   protected $lastItem = 0;
   protected $currentPage;
   protected $perPage = 0;
   protected $lastPage = 0;
   protected $onFirstPage = false;
   protected $hasMorePages = false;
   protected $items = [];

   /**
    * {@inheritdoc}
    */
   public function count() : int
   {
      return $this->count;
   }

   /**
    * @param int $count
    */
   public function setCount($count)
   {
      $this->count = $count;
   }

   /**
    * {@inheritdoc}
    */
   public function total() : int
   {
      return $this->total;
   }

   /**
    * @param int $total
    */
   public function setTotal($total)
   {
      $this->total = $total;
   }

   /**
    * {@inheritdoc}
    */
   public function firstItem() : int
   {
      return $this->firstItem;
   }

   /**
    * @param int $firstItem
    */
   public function setFirstItem($firstItem)
   {
      $this->firstItem = $firstItem;
   }

   /**
    * {@inheritdoc}
    */
   public function lastItem() : int
   {
      return $this->lastItem;
   }

   /**
    * @param int $lastItem
    */
   public function setLastItem($lastItem)
   {
      $this->lastItem = $lastItem;
   }

   /**
    * {@inheritdoc}
    */
   public function currentPage() : int
   {
      return $this->currentPage;
   }

   /**
    * @param mixed $currentPage
    */
   public function setCurrentPage($currentPage)
   {
      $this->currentPage = $currentPage;
   }

   /**
    * {@inheritdoc}
    */
   public function perPage() : int
   {
      return $this->perPage;
   }

   /**
    * @param int $perPage
    */
   public function setPerPage($perPage)
   {
      $this->perPage = $perPage;
   }

   /**
    * {@inheritdoc}
    */
   public function lastPage() : int
   {
      return $this->lastPage;
   }

   /**
    * @param int $lastPage
    */
   public function setLastPage($lastPage)
   {
      $this->lastPage = $lastPage;
   }

   /**
    * {@inheritdoc}
    */
   public function onFirstPage() : bool
   {
      return $this->onFirstPage;
   }

   /**
    * @param boolean $onFirstPage
    */
   public function setOnFirstPage($onFirstPage)
   {
      $this->onFirstPage = $onFirstPage;
   }

   /**
    * {@inheritdoc}
    */
   public function hasMorePages() : bool
   {
      return $this->hasMorePages;
   }

   /**
    * @param boolean $hasMorePages
    */
   public function setHasMorePages($hasMorePages)
   {
      $this->hasMorePages = $hasMorePages;
   }

   /**
    * {@inheritdoc}
    */
   public function items() : Collection
   {
      return $this->items;
   }

   /**
    * {@inheritdoc}
    */
   public function isEmpty() : bool
   {
      return count($this->items) === 0;
   }

   /**
    * @param array $items
    */
   public function setItems($items)
   {
      $this->items = $items;
   }

}
