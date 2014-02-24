<?php namespace Jacopo\Library;

use Illuminate\Support\ServiceProvider;
use Jacopo\Library\Email\SwiftMailer;

class LibraryServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('jacopo/library');

        $this->bindMailer();

    }

    protected function bindMailer()
    {
        $this->app->bind('jmailer', function ()
        {
            return new SwiftMailer;
        });
    }

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}