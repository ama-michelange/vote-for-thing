<?php
namespace App\Http\Controllers;

use App\Domain\Helper\ConvertHelper;
use App\Entity\Thing;
use App\Http\Transformers\ThingTransformer;

class ThingController extends ApiController
{
   /**
    * Eloquent model.
    *
    * @return \Illuminate\Database\Eloquent\Model
    */
   protected function model()
   {
      return new Thing();
   }

   /**
    * Transformer for the current model.
    *
    * @return \League\Fractal\TransformerAbstract
    */
   protected function transformer()
   {
      return new ThingTransformer();
   }

   /**
    * Get the validation rules for create.
    *
    * @return array
    */
   protected function rulesForCreate()
   {
      return [
         'title' => 'required|filled|string',
         'proper_title' => 'string',
         'number' => 'string',
         'image_url' => 'url',
         'description_url' => 'url',
         'legal' => 'date',
         'description' => 'string'
      ];
   }

   /**
    * Get the validation rules for update.
    *
    * @param int $id
    *
    * @return array
    */
   protected function rulesForUpdate($id)
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
   protected function transformBeforeSave($pData, $pItem = null)
   {
      if (isset($pData['title'])) {
         $title = $this->getValue('title', $pData, $pItem);
         $pData['lib_title'] = ConvertHelper::toLibrarianTitle($title);
      }
      return $pData;
   }
}
