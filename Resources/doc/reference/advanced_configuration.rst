Advanced Configuration
======================


.. code-block:: yaml

    sonata_classification:
        class:
            collection:     Application\Sonata\ClassificationBundle\Entity\Collection
            tag:            Application\Sonata\ClassificationBundle\Entity\Tag
            category:       Application\Sonata\ClassificationBundle\Entity\Category

    sonata_news:
        title:        Sonata Project
        link:         https://sonata-project.org
        description:  Cool bundles on top of Symfony2
        salt:         'secureToken'
        permalink_generator: brother.comment.permalink.date # brother.comment.permalink.collection
        permalink:
            date:     '%%1$04d/%%2$02d/%%3$02d/%%4$s' # => 2012/02/01/slug
        comment:
            notification:
                emails:   [email@example.org, email2@example.org]
                from:     no-reply@sonata-project.org
                template: 'BrotherCommentBundle:Mail:comment_notification.txt.twig'

        class:
            post:       Application\Brother\CommentBundle\Entity\Post
            comment:    Application\Brother\CommentBundle\Entity\Comment
            media:      Application\Sonata\MediaBundle\Entity\Media
            user:       Application\Sonata\UserBundle\Entity\User

        admin:
            post:
                class:       Brother\CommentBundle\Admin\PostAdmin
                controller:  SonataAdminBundle:CRUD
                translation: BrotherCommentBundle
            comment:
                class:       Brother\CommentBundle\Admin\CommentAdmin
                controller:  BrotherCommentBundle:CommentAdmin
                translation: BrotherCommentBundle
            collection:
                class:       Brother\CommentBundle\Admin\CollectionAdmin
                controller:  SonataAdminBundle:CRUD
                translation: BrotherCommentBundle
            tag:
                class:       Brother\CommentBundle\Admin\TagAdmin
                controller:  SonataAdminBundle:CRUD
                translation: BrotherCommentBundle

    doctrine:
        orm:
            entity_managers:
                default:
                    #metadata_cache_driver: apc
                    #query_cache_driver: apc
                    #result_cache_driver: apc
                    mappings:
                        ApplicationBrotherCommentBundle: ~
                        BrotherCommentBundle: ~



