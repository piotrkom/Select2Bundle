<?php

namespace CyberApp\Select2Bundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class CyberAppSelect2Extension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $serviceId = 'cyber_app.form.type.select2';
        $configs = $this->processConfiguration(new Configuration(), $configs);
        $types = ['choice', 'country', 'language', 'locale', 'timezone', 'currency', 'entity'];
        foreach ($types as $type) {
            $container->setDefinition(
                $serviceId . '.' .$type,
                (new DefinitionDecorator($serviceId))
                    ->addArgument($type)
                    ->addArgument($configs['configs'])
                    ->addTag('form.type', array('alias' => 'select2_' . $type))
            );
        }

        $container->setParameter('cyber_app.select2.configs', $configs['configs']);
    }
}
