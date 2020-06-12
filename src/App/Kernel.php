<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * @return iterable|\Symfony\Component\HttpKernel\Bundle\BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        $contents = require $this->getProjectDir().'/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->setParameter('container.dumper.inline_class_loader', $this->debug);
        $container->setParameter('container.dumper.inline_factories', true);
        $confDir = $this->getProjectDir().'/config';
        $loader->load($confDir.'/{packages}/*.yaml', 'glob');
        $loader->load($confDir.'/{packages}/'.$this->environment.'/*.yaml', 'glob');
        $loader->load($confDir.'/services.yaml');
        $loader->load($confDir.'/{services}_'.$this->environment.'.yaml', 'glob');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $confDir = $this->getProjectDir().'/config';
        $routes->import($confDir.'/{routes}/'.$this->environment.'/*.yaml', '/', 'glob');
        $routes->import($confDir.'/routes.yaml');
    }
}
