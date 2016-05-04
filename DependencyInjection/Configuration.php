<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brother\CommentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('brother_comment');

        $rootNode
            ->children()
                ->scalarNode('title')->isRequired()->end()
                ->scalarNode('link')->isRequired()->end()
                ->scalarNode('description')->isRequired()->end()
                ->scalarNode('permalink_generator')->defaultValue('brother.comment.permalink.date')->end()
                ->scalarNode('salt')->isRequired()->end()
                ->arrayNode('permalink')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('date')->defaultValue('%%1$04d/%%2$d/%%3$d/%%4$s')->end() // year/month/day/slug
                    ->end()
                ->end()
                ->arrayNode('table')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('post_tag')->defaultValue('news__post_tag')->end()
                    ->end()
                ->end()
                ->arrayNode('class')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('tag')->defaultValue('Application\\Sonata\\ClassificationBundle\\Entity\\Tag')->end()
                        ->scalarNode('collection')->defaultValue('Application\\Sonata\\ClassificationBundle\\Entity\\Collection')->end()
                        ->scalarNode('post')->defaultValue('Application\\Sonata\\CommentBundle\\Entity\\Post')->end()
                        ->scalarNode('comment')->defaultValue('Application\\Sonata\\CommentBundle\\Entity\\Comment')->end()
                        ->scalarNode('media')->defaultValue('Application\\Sonata\\MediaBundle\\Entity\\Media')->end()
                        ->scalarNode('user')->defaultValue('Application\\Sonata\\UserBundle\\Entity\\User')->end()
                    ->end()
                ->end()

                ->arrayNode('admin')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('post')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Brother\\CommentBundle\\Admin\\PostAdmin')->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue('SonataAdminBundle:CRUD')->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('BrotherCommentBundle')->end()
                            ->end()
                        ->end()
                        ->arrayNode('comment')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Brother\\CommentBundle\\Admin\\CommentAdmin')->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue('BrotherCommentBundle:CommentAdmin')->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('BrotherCommentBundle')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('comment')
                    ->children()
                        ->arrayNode('notification')
                            ->children()
                                ->arrayNode('emails')
                                    ->prototype('scalar')->cannotBeEmpty()->end()
                                ->end()
                                ->scalarNode('from')->cannotBeEmpty()->end()
                                ->scalarNode('template')->cannotBeEmpty()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
