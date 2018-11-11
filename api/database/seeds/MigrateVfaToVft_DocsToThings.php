<?php

use App\Entity\Category;
use App\Entity\Thing;
use App\Entity\V1\Doc;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;

class MigrateVfaToVft_DocsToThings extends MigrateVfaToVft_Base
{
   protected $vfaModelClass = Doc::class;
   protected $vftModelClass = Thing::class;

   /**
    * Convert VFA Entities Collections to VFT Entities and save it.
    * @param Collection $pVfaModels
    */
   protected function saveTo(Collection $pVfaModels) : void
   {
      // Search comic category
      $catComic = null;
      if (Schema::hasTable('categories')) {
         $catComic = Category::where('name', 'comic')->first();
      }
      if (false == isset($catComic)) {
         exit("Category 'comic' don't found!");
      }
      // Convert and save to Things
      VftMigrateTools::saveToThings($pVfaModels, $catComic);
   }

}
