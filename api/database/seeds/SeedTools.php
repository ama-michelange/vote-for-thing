<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class SeedTools
{

   /**
    * Convert the model to JSON and save it in file.
    * <p>The file is saved in 'storage/app/[$pPathname]/[TableNameOfModel].json'</p>
    * <p>Replace also some unicode characters by their real characters: &#039; &amp;</p>
    * @param Collection $pCollectionModel The model's collection to save in JSON file
    * @param string $pPathname The pathname where store the JSON file, default 'jsonseed'
    */
   public static function storeToJsonSeedFile(Collection $pCollectionModel, string $pPathname = 'jsonseed')
   {
      // Prepare
      $table = $pCollectionModel->first()->getTable();
      $file = $pPathname . '/' . $table . '.json';
      if (config('vft.migrate.verbose')) {
         printf("Storing: Table '%s' to File '%s'" . PHP_EOL, $table, $file);
      }
      // Encode to JSON
      $json = json_encode($pCollectionModel, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
      $json = str_replace('&#039;', "'", $json);
      $json = str_replace('&amp;', "&", $json);
      // Store
      Storage::makeDirectory($pPathname);
      Storage::put($file, $json);
   }

   /**
    * Load a JSON file and convert to a model's collection.
    * @param $pModelClass The class of model
    * @param string $pPathname The pathname where store the JSON file, default 'jsonseed'
    * @return Collection The filled collection
    */
   public static function loadFromJsonSeedFile($pModelClass, string $pPathname = 'jsonseed')
   {
      // Prepare
      $table = with(new $pModelClass)->getTable();
      $file = $pPathname . '/' . $table . '.json';
      if (config('vft.migrate.verbose')) {
         printf("Loading: File '%s'" . PHP_EOL, $file);
      }
      // Load file
      $json = Storage::get($file);
      // Decode from JSON string
      $array = json_decode($json, true);
      // Convert to model and add to the collection
      $coll = new Collection();
      foreach ($array as $item) {
         $model = new $pModelClass;
         $model->fill($item);
         $coll->add($model);
      }
      return $coll;
   }
}
