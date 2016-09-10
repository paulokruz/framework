<?php

namespace Module;

use Config\Repository as Config;
use Foundation\Application;


class ModuleManager
{
    /**
     * @var \Foundation\Application
     */
    protected $app;

    /**
     * @var \Config\Repository
     */
    protected $config;


    /**
     * Create a new ModuleRepository instance.
     *
     * @param Application         $app
     * @param RepositoryInterface $repository
     */
    public function __construct(Application $app, Config $config)
    {
        $this->app = $app;

        $this->config = $config;
    }

    /**
     * Register the module service provider file from all modules.
     *
     * @return mixed
     */
    public function register()
    {
        $modules = $this->getRepository();

        foreach ($modules as $module => $config) {
            if (isset($config['enabled']) && is_bool($config['enabled'])) {
                $enabled = $config['enabled'];
            } else {
                $enabled = true;
            }

            if (! $enabled) continue;

            //
            $this->registerServiceProvider($module);

            $this->autoloadFiles($module, $config);
        };
    }

    /**
     * Register the Module Service Provider.
     *
     * @param string $config
     *
     * @return string
     */
    protected function registerServiceProvider($module)
    {
        $serviceProvider = $this->getModulesNamespace() ."\\{$module}\\Providers\\{$module}ServiceProvider";

        if (class_exists($serviceProvider)) {
            $this->app->register($serviceProvider);
        }
    }

    /**
     * Autoload custom module files.
     *
     * @param array $config
     */
    protected function autoloadFiles($module, $config)
    {
        $autoload = array('config', 'events', 'filters', 'routes');

        // Calculate the names of the files to be autoloaded.
        if (isset($config['autoload']) && is_array($config['autoload'])) {
            $autoload = array_values(array_intersect($config['autoload'], $autoload));
        }

        array_push($autoload, 'bootstrap');

        // Calculate the Modules path.
        $basePath = $this->getModulesPath() .DS .$module .DS;

        foreach ($autoload as $name) {
            $path = $basePath .ucfirst($name) .'.php';

            if (is_readable($path)) require $path;
        }
    }

    public function getModulesPath()
    {
        return $this->config->get('modules.path');
    }

    public function getModulesNamespace()
    {
        return $this->config->get('modules.namespace');
    }

    public function getRepository()
    {
        return $this->config->get('modules.repository');
    }

    /**
     * Return the name of the registered Modules.
     *
     * @return array
     */
    public function getModules()
    {
        $modules = $this->getRepository();

        return array_keys($modules);
    }
    
}
