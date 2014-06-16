<?php
namespace Tnarik\RocketeerDatabase;

use Illuminate\Container\Container;

use Rocketeer\TasksHandler;
use Rocketeer\Traits\Plugin;

class RocketeerDatabase extends Plugin
{
  /**
   * Setup the plugin
   */
  public function __construct(Container $app) {
    parent::__construct($app);
    $this->configurationFolder = __DIR__.'/../config';
  }

  /**
   * Bind additional classes to the Container
   *
   * @param Container $app
   *
   * @return void
   */
  public function register(Container $app) {
    return $app;
  }

  /**
  * Register Tasks with Rocketeer
  *
  * @param TasksQueue $queue
  *
  * @return void
  */
  public function onQueue(TasksHandler $handler) {

    // The following doesn't work, therefore the uncommented code
    // $handler->add('Tnarik\RocketeerDatabase\InitializeDatabase');
    $tasks = ['Tnarik\RocketeerDatabase\InitializeDatabase', 'Tnarik\RocketeerDatabase\CreateDatabase' ];
    foreach ( $tasks as $task ) {
      $taskInstance = $handler->buildTaskFromClass($task);
      $slug = $taskInstance->getSlug();
      $handle = 'rocketeer.tasks.'.$slug;
  
      $this->app->bind($handle, function () use ($taskInstance) {
        return $taskInstance;
      });
      $command = trim("deploy.".$slug,".");
  
      $this->app->singleton($command, function () use ($taskInstance, $slug) {
        return new \Rocketeer\Commands\BaseTaskCommand($taskInstance, $slug);
      });
      $this->app['rocketeer.console']->add($this->app[$command]);
    }

    $handler->after('deploy', function ($task) {
      if ( $task->command->option("migrate") && $task->command->option("seed")) {
        $task->command->info("Migrations and seeds ctivated: database will be initialized");
    
        $this->initialize();

      } else {
        $task->command->info("Migrations and seeds deactivated");
      }
    });

    $handler->addTaskListeners('deploy', 'runComposer', function($task) {
      if ( $task->command->option("migrate") ) {
        $task->command->info("Migrations activated: database will be created");
    
         $this->create();
   
      } else {
        $task->command->info("Migrations deactivated");
      }
    });
  }

  public function create() {
    $this->command->info($this->config->get('rocketeer-database::config.import_seeder'));


    \Rocketeer::execute(array('Tnarik\RocketeerDatabase\CreateDatabase'));
  }

  public function initialize() {
    \Rocketeer::execute(array('Tnarik\RocketeerDatabase\InitializeDatabase'));
  }
}
