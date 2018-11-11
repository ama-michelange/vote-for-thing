<?php

use App\Entity\Category;
use Illuminate\Database\Seeder;

class InitDatabaseSeeder_CategoriesTable extends Seeder
{
   /**
    * Run the database seeds.
    *
    * @return void
    */
   public function run()
   {
      $this->createIfNotExist(['name', 'comic'], ['name' => 'comic']);
      $this->createIfNotExist(['name', 'book'], ['name' => 'book']);
      $this->createIfNotExist(['name', 'movie'], ['name' => 'movie']);
   }

   /**
    * Create a category if not already exists.
    * @param array $aWhere The where array
    * @param array $aToCreate The create array
    */
   private function createIfNotExist($aWhere, $aToCreate)
   {
      $model = Category::where($aWhere[0], $aWhere[1])->first();
      if (false == isset($model)) {
         Category::create($aToCreate);
      }
   }
}
