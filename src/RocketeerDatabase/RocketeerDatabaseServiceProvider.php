<?php
namespace Tnarik\RocketeerDatabase;

use Illuminate\Support\ServiceProvider;
use Rocketeer\Facades\Rocketeer;

/**
 * Register the Database plugin with the Laravel framework and Rocketeer
 */
class RocketeerDatabaseServiceProvider extends ServiceProvider {

  /**
   * Indicates if loading of the provider is deferred.
   *
   * @var bool
   */
  protected $defer = false;

  /**
   * Register classes
   *
   * @return void
   */
  public function register() {
/*
    $this->app['rocketeer-database'] = $this->app->share(function($app){
      return new RocketeerDatabase($app);
    });
*/
    $this->commands("deploy.dbinit");
    $this->commands("deploy.dbcreate");

  }

  /**
   * Boot the plugin
   *
   * @return void
   */
  public function boot() {
    Rocketeer::plugin('Tnarik\RocketeerDatabase\RocketeerDatabase');
  }

  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides() {
    return array();
  }

}
