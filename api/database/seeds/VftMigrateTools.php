<?php
use App\Entity\V1\Doc as VfaDoc;
use App\Entity\V1\Group as VfaGroup;
use Domain\Entity\CategoryEntity;
use Domain\Entity\GroupEntity;
use Domain\Entity\ThingEntity;
use Domain\Helper\ConvertHelper;
use Illuminate\Database\Eloquent\Collection;

class VftMigrateTools
{
   /**
    * Convert a collection of VFA Doc to VFT Things and save they to database.
    * @param Collection $pDocs
    * @param \Domain\Entity\CategoryEntity $pCategory
    */
   public static function saveToThings(Collection $pDocs, CategoryEntity $pCategory)
   {
      foreach ($pDocs as $doc) {
         static::saveToThing($doc, $pCategory);
      }
   }

   /**
    * Convert a VFA v1 Doc to Thing and save it to database.
    * @param VfaDoc $pDoc The document to convert
    * @param \Domain\Entity\CategoryEntity $pCategory The category of the new thing
    */
   public static function saveToThing(VfaDoc $pDoc, CategoryEntity $pCategory)
   {
      $aField = [
         'id' => $pDoc->doc_id,
         'category_id' => $pCategory->id,
         'title' => ConvertHelper::decodeHtmlSpecialChars($pDoc->title),
         'proper_title' => ConvertHelper::decodeHtmlSpecialChars($pDoc->proper_title),
         'lib_title' => ConvertHelper::toLibrarianTitle(ConvertHelper::decodeHtmlSpecialChars($pDoc->title)),
         'number' => $pDoc->number,
         'image_url' => $pDoc->image,
         'description_url' => $pDoc->url,
         'legal' => $pDoc->date_legal
      ];
      ThingEntity::create($aField);
   }

   /**
    * Convert a collection of VFA Group to VFT Group and save they to database.
    * @param Collection $pCollection
    */
   public static function saveToGroups(Collection $pCollection)
   {
      foreach ($pCollection as $item) {
         static::saveToGroup($item);
      }
   }

   /**
    * Convert a VFA v1 Doc to Thing and save it to database.
    * @param VfaGroup $pGroup The document to convert
    */
   public static function saveToGroup(VfaGroup $pGroup)
   {
      $aField = [
         'id' => $pGroup->group_id,
         'name' => ConvertHelper::decodeHtmlSpecialChars($pGroup->group_name)
      ];
      GroupEntity::create($aField);
   }
}
