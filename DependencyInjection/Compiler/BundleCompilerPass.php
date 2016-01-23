<?php

namespace CyberApp\Select2Bundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class BundleCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        // get resources value
        $resources   = $container->getParameter('twig.form.resources');
        $resources[] = 'CyberAppSelect2Bundle::bootstrap3.html.twig';
        $resources[] = 'CyberAppSelect2Bundle::javascript.html.twig';

        // update resources value
        $container->setParameter('twig.form.resources', $resources);
    }
}
