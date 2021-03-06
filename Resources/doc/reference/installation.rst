Installation
============

* Add BrotherCommentBundle and dependencies to your ``composer.json`` file:

.. code-block:: bash

    composer require sonata-project/news-bundle "dev-master" --no-update
    composer require sonata-project/doctrine-orm-admin-bundle "dev-master" --no-update
    composer require sonata-project/easy-extends-bundle "dev-master" --no-update
    composer require friendsofsymfony/rest-bundle "~1.1" --no-update
    composer require nelmio/api-doc-bundle "~0.1|~1.0" --no-update
    composer require sonata-project/classification-bundle "~2.2@dev"


``friendsofsymfony/rest-bundle`` and ``nelmio/api-doc-bundle`` are needed only
if you use the API.


* Add BrotherCommentBundle to your application kernel:

.. code-block:: php

    <?php

    // app/AppKernel.php

    // ...
    public function registerBundles()
    {
        return array(
            // ...
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\MarkItUpBundle\SonataMarkItUpBundle(),
            new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
            new Brother\CommentBundle\BrotherCommentBundle(),
            new Sonata\UserBundle\SonataUserBundle(),
            new Sonata\MediaBundle\SonataMediaBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),
            new Sonata\IntlBundle\SonataIntlBundle(),
            new Sonata\FormatterBundle\SonataFormatterBundle(),
            new Sonata\ClassificationBundle\SonataClassificationBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
        );
    }


* Create a configuration file called ``sonata_news.yml``:

.. code-block:: yaml

    # app/config/sonata_news.yml

    sonata_news:
        title:        Sonata Project
        link:         https://sonata-project.org
        description:  Cool bundles on top of Symfony2
        salt:         'secureToken'
        permalink_generator: brother.comment.permalink.date # brother.comment.permalink.collection

        comment:
            notification:
                emails:   [email@example.org, email2@example.org]
                from:     no-reply@sonata-project.org
                template: 'BrotherCommentBundle:Mail:comment_notification.txt.twig'

    doctrine:
        orm:
            entity_managers:
                default:
                    #metadata_cache_driver: apc
                    #query_cache_driver: apc
                    #result_cache_driver: apc
                    mappings:
                        #ApplicationBrotherCommentBundle: ~
                        BrotherCommentBundle: ~


* Import the ``sonata_news.yml`` file and enable json type for doctrine:

.. code-block:: yaml

    # app/config/config.yml

    imports:
        # ...
        - { resource: sonata_news.yml }
    # ...
    doctrine:
        dbal:
        # ...
            types:
                json: Sonata\Doctrine\Types\JsonType


* Add a new context into your ``sonata_media.yml`` configuration if you don't have go there https://sonata-project.org/bundles/media/master/doc/reference/installation.html:

.. code-block:: yaml

    # app/config/sonata_media.yml

    news:
        providers:
            - sonata.media.provider.dailymotion
            - sonata.media.provider.youtube
            - sonata.media.provider.image

        formats:
            small: { width: 150 , quality: 95}
            big:   { width: 500 , quality: 90}

* Create configuration file ``sonata_formatter.yml`` the text formatters available for your blog post:


.. code-block:: yaml

    # app/config/sonata_formatter.yml

    sonata_formatter:
        formatters:
            markdown:
                service: sonata.formatter.text.markdown
                extensions:
                    - sonata.formatter.twig.control_flow
                    - sonata.formatter.twig.gist
                    - sonata.media.formatter.twig

            text:
                service: sonata.formatter.text.text
                extensions:
                    - sonata.formatter.twig.control_flow
                    - sonata.formatter.twig.gist
                    - sonata.media.formatter.twig

            rawhtml:
                service: sonata.formatter.text.raw
                extensions:
                    - sonata.formatter.twig.control_flow
                    - sonata.formatter.twig.gist
                    - sonata.media.formatter.twig

            richhtml:
                service: sonata.formatter.text.raw
                extensions:
                    - sonata.formatter.twig.control_flow
                    - sonata.formatter.twig.gist
                    - sonata.media.formatter.twig


* Generate the application bundles:

.. code-block:: bash

    php app/console sonata:easy-extends:generate BrotherCommentBundle -d src
    php app/console sonata:easy-extends:generate SonataUserBundle -d src
    php app/console sonata:easy-extends:generate SonataMediaBundle -d src
    php app/console sonata:easy-extends:generate SonataClassificationBundle -d src


* Enable the application bundles:

.. code-block:: php

    <?php

    // app/AppKernel.php

    // ...
    public function registerBundles()
    {
        return array(
            // ...
            new Application\Brother\CommentBundle\ApplicationBrotherCommentBundle(),
            new Application\Sonata\UserBundle\ApplicationSonataUserBundle(),
            new Application\Sonata\MediaBundle\ApplicationSonataMediaBundle(),
            new Application\Sonata\ClassificationBundle\ApplicationSonataClassificationBundle(),
        );
    }


* Uncomment the ApplicationBrotherCommentBundle mapping inside ``sonata_news.yml`` :

.. code-block:: yaml

    # app/config/sonata_news.yml

    doctrine:
        orm:
            entity_managers:
                default:
                    # ...
                    mappings:
                        ApplicationBrotherCommentBundle: ~
                        BrotherCommentBundle: ~


* Update database schema by running command ``php app/console doctrine:schema:update --force``

* Complete the FOS/UserBundle install and use the ``Application\Sonata\UserBundle\Entity\User`` as the user class

* Add BrotherCommentBundle routes to your application routing.yml:

.. code-block:: yaml

    # app/config/routing.yml

    news:
        resource: '@BrotherCommentBundle/Resources/config/routing/news.xml'
        prefix: /news

