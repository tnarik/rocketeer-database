<?php

namespace Tnarik\RocketeerDatabase;

use Illuminate\Config\Repository as Config;

class CreateDatabase extends \Rocketeer\Traits\Task {

  protected $name = 'dbcreate';

  protected $description = 'Creates the database for the corresponding connection';

  public function execute() {
    $this->command->info("Creating database");

    $connection = $this->rocketeer->getConnection();

    // Load configuration for the environment (based on the Rocketeer connection) without creating a new app instance
    $config = new Config(
      $this->app->getConfigLoader(), $connection
    );

    $this->command->info("Environment detected: ".$config->getEnvironment());

    switch ($config->get('database.default')) {
      case 'sqlite':
        $this->command->info("Creating sqlite database");

        // Rewrite the database local path for the current release on the remote
        $remote_path = str_replace($this->app['path'], "", $config->get('database.connections.sqlite.database'));
    
        $this->runForCurrentRelease("touch ".$this->releasesManager->getCurrentReleasePath("{path}".DIRECTORY_SEPARATOR.$remote_path));
        $this->setPermissions("{path}".$remote_path);
        $this->setPermissions(dirname("{path}".$remote_path));

        break;
    }
    $this->command->info("Created database");
  }
}
