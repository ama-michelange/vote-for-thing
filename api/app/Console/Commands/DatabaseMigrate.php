<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DatabaseMigrate extends Command
{
   /**
    * The name and signature of the console command.
    *
    * @var string
    */
   protected $signature = 'db:migrate
                           {--from=db : Loading from : db (database) or json (JSON files)}
                           {--json : When loading from database, save all migrating tables in JSON files}';

   /**
    * The console command description.
    *
    * @var string
    */
   protected $description = 'Migrate database from VFA to VFT';

   /**
    * Create a new command instance.
    *
    * @return void
    */
   public function __construct()
   {
      parent::__construct();
   }

   /**
    * Execute the console command.
    *
    * @return mixed
    */
   public function handle()
   {
      // Transform option to config access by seeder
      config(['vft.migrate.from' => $this->option('from')]);
      $json = $this->option('json');
      if ($json) {
         config(['vft.migrate.json' => true]);
      }
      // Verbose
      config(['vft.migrate.verbose' => true]);
      $quiet = $this->option('quiet');
      if ($quiet) {
         config(['vft.migrate.verbose' => false]);
      }
      // Calls
      $this->call('db:seed', ['--class' => 'MigrateVfaToVft']);
   }
}
