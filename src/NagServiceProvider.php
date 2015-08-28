<?php

namespace DragonFly\Nag;

use Collective\Html\HtmlServiceProvider;

class NagServiceProvider extends HtmlServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        parent::register();
    }

    public function boot()
    {
        $config = __DIR__ . '/../config/nag.php';
        $this->publishes([
            $config                          => config_path('nag.php'),
            __DIR__ . '/../resources/assets' => public_path('assets/js'),
        ]);

        $this->mergeConfigFrom($config, 'validateui');

        include(__DIR__.'/Http/routes.php');
    }

    /**
     * {@inheritdoc}
     */
    protected function registerFormBuilder()
    {
        $this->app->bindShared('form', function ($app)
        {
            $form = new FormBuilder($app['html'], $app['url'], $app['session.store']->getToken());

            return $form->setSessionStore($app['session.store']);
        });

	    $this->app->bind('ConvertersContract', function($app) {
		    $class = 'DragonFly\Nag\Converters'.$app['config']->get('nag.driver', 'FormValidation');

		    return new $class;
	    });
    }
}
