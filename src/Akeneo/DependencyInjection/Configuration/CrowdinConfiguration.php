<?php

namespace Akeneo\DependencyInjection\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class CrowdinConfiguration
 *
 * @author    Clement Gautier <clement.gautier@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CrowdinConfiguration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();

        return $builder
            ->root('crowdin')
                ->children()
                    ->integerNode('min_translated_progress')
                        ->isRequired()
                    ->end()
                    ->arrayNode('download')
                        ->isRequired()
                        ->children()
                            ->scalarNode('base_dir')->isRequired()->end()

                            ->arrayNode('locale_map')
                                ->isRequired()
                                ->useAttributeAsKey('name')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                    ->scalarNode('project')->isRequired()->end()
                    ->scalarNode('key')->isRequired()->end()
                    ->arrayNode('upload')
                        ->isRequired()
                        ->children()
                            ->scalarNode('base_dir')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
