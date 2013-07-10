<?php

namespace Meot\FormBundle;

use KULeuven\ShibbolethBundle\DependencyInjection\Security\ShibbolethFactory;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MeotFormBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new ShibbolethFactory());
    }
}
