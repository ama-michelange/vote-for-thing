<?php
namespace App\Http\Transformers;

use Domain\Entity\CategoryEntity;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
   /**
    * Turn this item object into a generic array.
    *
    * @param $item
    * @return array
    */
   public function transform(CategoryEntity $item)
   {
      $ret = array();
      if (false == is_null($item->id)) {
         $ret['id'] = (int) $item->id;
      }
      if ($item->name) {
         $ret['name'] = (string) $item->name;
      }
      if ($item->created_at) {
         $ret['created_at'] = (string) $item->created_at;
      }
      if ($item->updated_at) {
         $ret['updated_at'] = (string) $item->updated_at;
      }
      return $ret;
   }
}
