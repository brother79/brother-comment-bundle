<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brother\CommentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * BrotherCommentBundleExtension.
 *
 * @author      Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class BrotherCommentExtension extends Extension
{
    /**
     * @throws \InvalidArgumentException
     *
     * @param array                                                   $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);
        $bundles = $container->getParameter('kernel.bundles');

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');


        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('orm.xml');
        $loader->load('twig.xml');
        $loader->load('form.xml');
        $loader->load('core.xml');
        $loader->load('block.xml');
        $loader->load('serializer.xml');

        if (isset($bundles['FOSRestBundle']) && isset($bundles['NelmioApiDocBundle'])) {
            $loader->load('api_controllers.xml');
            $loader->load('api_form.xml');
        }

        if (isset($bundles['SonataAdminBundle'])) {
            $loader->load('admin.xml');
        }

        if (!isset($config['salt'])) {
            throw new \InvalidArgumentException("The configuration node 'salt' is not set for the BrotherCommentBundle (sonata_news)");
        }

        if (!isset($config['comment'])) {
            throw new \InvalidArgumentException("The configuration node 'comment' is not set for the BrotherCommentBundle (sonata_news)");
        }

        $container->getDefinition('brother.comment.hash.generator')
            ->replaceArgument(0, $config['salt']);

        $container->getDefinition('brother.comment.permalink.date')
            ->replaceArgument(0, $config['permalink']['date']);

        $container->setAlias('brother.comment.permalink.generator', $config['permalink_generator']);

        $container->getDefinition('brother.comment.hash.generator')
            ->replaceArgument(0, $config['salt']);

        $container->getDefinition('brother.comment.mailer')
            ->replaceArgument(4, array(
                'notification' => $config['comment']['notification'],
            ));

        $this->registerDoctrineMapping($config, $container);
        $this->configureClass($config, $container);
        $this->configureAdmin($config, $container);
    }

    /**
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function configureClass($config, ContainerBuilder $container)
    {
        // admin configuration
        $container->setParameter('brother.comment.admin.post.entity',       $config['class']['post']);
        $container->setParameter('brother.comment.admin.comment.entity',    $config['class']['comment']);

        // manager configuration
        $container->setParameter('brother.comment.manager.post.entity',     $config['class']['post']);
        $container->setParameter('brother.comment.manager.comment.entity',  $config['class']['comment']);
    }

    /**
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function configureAdmin($config, ContainerBuilder $container)
    {
        $container->setParameter('brother.comment.admin.post.class',              $config['admin']['post']['class']);
        $container->setParameter('brother.comment.admin.post.controller',         $config['admin']['post']['controller']);
        $container->setParameter('brother.comment.admin.post.translation_domain', $config['admin']['post']['translation']);

        $container->setParameter('brother.comment.admin.comment.class',              $config['admin']['comment']['class']);
        $container->setParameter('brother.comment.admin.comment.controller',         $config['admin']['comment']['controller']);
        $container->setParameter('brother.comment.admin.comment.translation_domain', $config['admin']['comment']['translation']);
    }

    /**
     * @param array $config
     */
    public function registerDoctrineMapping(array $config)
    {
        $collector = DoctrineCollector::getInstance();

        foreach ($config['class'] as $type => $class) {
            if (!class_exists($class)) {
                return;
            }
        }

        $collector->addAssociation($config['class']['post'], 'mapOneToMany', array(
            'fieldName'    => 'comments',
            'targetEntity' => $config['class']['comment'],
            'cascade'      => array(
                    0 => 'remove',
                    1 => 'persist',
                ),
            'mappedBy'      => 'post',
            'orphanRemoval' => true,
            'orderBy'       => array(
                    'createdAt' => 'DESC',
                ),
        ));

        $collector->addAssociation($config['class']['post'], 'mapManyToOne', array(
            'fieldName'    => 'image',
            'targetEntity' => $config['class']['media'],
            'cascade'      => array(
                    0 => 'remove',
                    1 => 'persist',
                    2 => 'refresh',
                    3 => 'merge',
                    4 => 'detach',
                ),
            'mappedBy'    => null,
            'inversedBy'  => null,
            'joinColumns' => array(
                    array(
                        'name'                 => 'image_id',
                        'referencedColumnName' => 'id',
                    ),
                ),
            'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['post'], 'mapManyToOne', array(
            'fieldName'    => 'author',
            'targetEntity' => $config['class']['user'],
            'cascade'      => array(
                    1 => 'persist',
                ),
            'mappedBy'    => null,
            'inversedBy'  => null,
            'joinColumns' => array(
                    array(
                        'name'                 => 'author_id',
                        'referencedColumnName' => 'id',
                    ),
                ),
            'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['post'], 'mapManyToOne', array(
            'fieldName'    => 'collection',
            'targetEntity' => $config['class']['collection'],
            'cascade'      => array(
                    1 => 'persist',
                ),
            'mappedBy'    => null,
            'inversedBy'  => null,
            'joinColumns' => array(
                    array(
                        'name'                 => 'collection_id',
                        'referencedColumnName' => 'id',
                    ),
                ),
            'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['post'], 'mapManyToMany', array(
            'fieldName'    => 'tags',
            'targetEntity' => $config['class']['tag'],
            'cascade'      => array(
                    1 => 'persist',
                ),
            'joinTable' => array(
                    'name'        => $config['table']['post_tag'],
                    'joinColumns' => array(
                            array(
                                'name'                 => 'post_id',
                                'referencedColumnName' => 'id',
                            ),
                        ),
                    'inverseJoinColumns' => array(
                            array(
                                'name'                 => 'tag_id',
                                'referencedColumnName' => 'id',
                            ),
                        ),
                ),
        ));

        $collector->addAssociation($config['class']['comment'], 'mapManyToOne', array(
            'fieldName'    => 'post',
            'targetEntity' => $config['class']['post'],
            'cascade'      => array(
            ),
            'mappedBy'    => null,
            'inversedBy'  => 'comments',
            'joinColumns' => array(
                    array(
                        'name'                 => 'post_id',
                        'referencedColumnName' => 'id',
                        'nullable'             => false,
                    ),
                ),
            'orphanRemoval' => false,
        ));
    }
}
