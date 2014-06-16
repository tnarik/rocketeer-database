<?php

namespace Tnarik\RocketeerDatabase;

use Illuminate\Config\Repository as Config;

class InitializeDatabase extends \Rocketeer\Traits\Task {

  protected $name = 'dbinit';

  protected $description = 'Initializes the database for the corresponding connection';

  public function execute() {
    $currentReleasePath = $this->releasesManager->getCurrentReleasePath();
  
    $import_seeder = $this->config->get('rocketeer-database::config.import_seeder');

    $import_seeder_path = $this->app['path.base'].DIRECTORY_SEPARATOR."app/database/seeds".DIRECTORY_SEPARATOR.$import_seeder.".php";

    if ( file_exists($import_seeder_path) ) {
      $this->command->info("Initializing database using ${import_seeder}");

      $this->remote->put($import_seeder_path, $currentReleasePath.DIRECTORY_SEPARATOR."app/database/seeds/ImportTableSeeder.php");

      $this->runArtisan('db:seed', array('class' => "ImportTableSeeder"));

      $this->command->info("Initialized database");
    }
  }
}
