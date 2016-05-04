<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Sonata Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brother\CommentBundle\Permalink;

use Brother\CommentBundle\Model\PostInterface;

interface PermalinkInterface
{
    /**
     * @param PostInterface $post
     */
    public function generate(PostInterface $post);

    /**
     * @param string $permalink
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function getParameters($permalink);
}
