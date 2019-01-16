<?php

namespace App\Http\Resources;


use App\Http\Transformers\ThingTransformer;
use Domain\Entity\ThingEntity;
use Domain\Helper\ConvertHelper;
use Domain\Query\QueryImp;

class ThingResource extends Resource
{
   /**
    * Constructor.
    */
   public function __construct()
   {
      parent::__construct();
      $this->entity = new ThingEntity();
      $this->query = new QueryImp($this->entity);
   }
   /**
    * {@inheritdoc}
    */
   protected function transformer()
   {
      return new ThingTransformer();
   }

   
   /**
    * {@inheritDoc}
    */
   public function rulesForCreate()
   {
      return [
         'title' => 'required|filled|string',
         'proper_title' => 'string',
         'number' => 'string',
         'image_url' => 'url',
         'description_url' => 'url',
         'legal' => 'date',
         'description' => 'string',
         'category.data.id' => 'required|filled|alpha_num'
      ];
   }

   /**
    * {@inheritDoc}
    */
   public function rulesForUpdate($id)
   {
      return [
         'title' => 'filled|string',
         'proper_title' => 'string',
         'number' => 'string',
         'image_url' => 'url',
         'description_url' => 'url',
         'legal' => 'date',
         'description' => 'string'
      ];
   }

   /**
    * {@inheritDoc}
    */
   public function transformBeforeSave($pData, $pItem = null)
   {
      if (isset($pData['title'])) {
         $title = $this->getValue('title', $pData, $pItem);
         $pData['lib_title'] = ConvertHelper::toLibrarianTitle($title);
      }
      return $pData;
   }
}
