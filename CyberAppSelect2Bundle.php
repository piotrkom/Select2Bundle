<?php

namespace CyberApp\Select2Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use CyberApp\Select2Bundle\DependencyInjection\Compiler\BundleCompilerPass;

class CyberAppSelect2Bundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new BundleCompilerPass());

        parent::build($container);
    }
}