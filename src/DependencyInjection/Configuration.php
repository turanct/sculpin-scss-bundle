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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration.
 *
 * @author Ioannis Kappas <ikappas@devworks.gr>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder;

        $rootNode = $treeBuilder->root('sculpin_scss');

        $rootNode
            ->children()
                ->scalarNode('formatter_class')
                    ->defaultValue('Leafo\\ScssPhp\\Formatter\\Compressed')
                ->end()
                ->arrayNode('extensions')
                    ->defaultValue(['scss'])
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('files')
                    ->defaultValue([])
                    ->prototype('scalar')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
