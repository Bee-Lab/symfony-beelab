<?php

namespace Infrastructure;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

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

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../../config/routes.yaml');
        $routes->import('../../config/{routes}/'.$this->environment.'/*.yaml');
    }
}
