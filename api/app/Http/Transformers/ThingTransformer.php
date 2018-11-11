<?php
namespace App\Http\Transformers;

use App\Entity\Thing;
use League\Fractal\TransformerAbstract;

class ThingTransformer extends TransformerAbstract
{
   /**
    * Turn this item object into a generic array.
    *
    * @param $item
    * @return array
    */
   public function transform(Thing $item)
   {
      $ret = array();
      if (false == is_null($item->id)) {
         $ret['id'] = (int) $item->id;
      }
      if ($item->title) {
         $ret['title'] = (string) $item->title;
      }
      if ($item->lib_title) {
         $ret['lib_title'] = (string) $item->lib_title;
      }
      if ($item->proper_title) {
         $ret['proper_title'] = (string) $item->proper_title;
      }
      if ($item->number) {
         $ret['number'] = (string) $item->number;
      }
      if ($item->legal) {
         $ret['legal'] = (string) $item->legal;
      }
      if ($item->image_url) {
         $ret['image_url'] = (string) $item->image_url;
      }
      if ($item->description_url) {
         $ret['description_url'] = (string) $item->description_url;
      }
      if ($item->description) {
         $ret['description'] = (string) $item->description;
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
