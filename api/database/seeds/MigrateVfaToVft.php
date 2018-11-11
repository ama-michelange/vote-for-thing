<?php

use Illuminate\Database\Seeder;

class MigrateVfaToVft extends Seeder
{
   /**
    * Seed the application's database.
    *
    * @return void
    */
   public function run()
   {
      $this->call(InitDatabaseSeeder_CategoriesTable::class);
      $this->call(MigrateVfaToVft_DocsToThings::class);
      $this->call(MigrateVfaToVft_GroupsToGroups::class);
   }
}
