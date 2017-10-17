<?php
/**
 * This file is part of Sculpin Scss Bundle.
 *
 * (c) DevWorks Greece
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevWorks\Sculpin\Bundle\ScssBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Sculpin Scss Extension.
 *
 * @author Ioannis Kappas <ikappas@devworks.gr>
 */
class SculpinScssExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration;
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter('sculpin_scss.formatter.class', $config['formatter_class']);
        $container->setParameter('sculpin_scss.extensions', $config['extensions']);
        $container->setParameter('sculpin_scss.files', $config['files']);

        $container->findDefinition('sculpin_scss.event.scss_converter')
                  ->addArgument($config['formatter_class'])
                  ->addArgument($config['extensions'])
                  ->addArgument($config['files']);
    }
}
