<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

abstract class MigrateVfaToVft_Base extends Seeder
{
   protected $vfaModelClass;
   protected $vftModelClass;

   /**
    * @return mixed
    */
   protected function getVfaModelClass()
   {
      return $this->vfaModelClass;
   }

   /**
    * @return mixed
    */
   protected function getVftModelClass()
   {
      return $this->vftModelClass;
   }


   /**
    * Convert VFA Entities Collections to VFT Entities and save it.
    * @param Collection $pVfaModels
    */
   abstract protected function saveTo(Collection $pVfaModels) : void;

   /**
    * Run the database seeds.
    *
    * @return void
    */
   public function run()
   {
      $vfaClass = $this->getVfaModelClass();
      $vftClass = $this->getVftModelClass();

      $vfaModels = null;
      $from = config('vft.migrate.from', 'db');
      switch ($from) {
         case 'db':
            // Read VFA from database table
            $vfaModels = $vfaClass::all();
            // Copy to JSON file
            if (config('vft.migrate.json')) {
               SeedTools::storeToJsonSeedFile($vfaModels);
            }
            break;
         case 'json':
            // Read VFA from JSON file
            $vfaModels = SeedTools::loadFromJsonSeedFile($vfaClass);
            break;
         default:
            exit("Configuration error! Unknown configuration for 'vft.migrate.from': " . $from);
      }
      // Clear VFT table
      $vftClass::truncate();
      // Convert and save
      $this->saveTo($vfaModels);
   }

}
