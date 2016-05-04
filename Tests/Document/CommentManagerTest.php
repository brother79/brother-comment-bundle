<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brother\CommentBundle\Tests\Document;

use Brother\CommentBundle\Document\CommentManager;

/**
 * Class CommentManagerTest.
 *
 * Tests the comment manager document.
 */
class CommentManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testImplements()
    {
        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $postManager = $this->getMock('Brother\CommentBundle\Model\PostManagerInterface');

        $commentManager = new CommentManager('Brother\CommentBundle\Document\BaseComment', $registry, $postManager);

        $this->assertInstanceOf('Sonata\CoreBundle\Model\PageableManagerInterface', $commentManager);
    }
}
