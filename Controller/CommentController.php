<?php

namespace Brother\CommentBundle\Controller;

use AppBundle\Entity\Comment\Comment;
use Brother\CommentBundle\Entity\CommentManager;
use Brother\CommonBundle\AppDebug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Comment controller.
 *
 */
class CommentController extends Controller
{
    /**
     * @var CommentManager
     */
    private $manager = null;

    /**
     * @return CommentManager
     */
    private function getManager()
    {
        if ($this->manager == null) {
            $this->manager = $this->container->get('brother.comment.manager.comment');
        }
        return $this->manager;
    }

    /**
     * Lists all Comment entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BrotherCommentBundle:Comment')->findAll();

        return $this->render('BrotherCommentBundle:Comment:index.html.twig', array(
            'entities' => $entities,
        ));
    }
        
    public function listAction(Request $request)
    {
        $this->initPager($request);
    }

    private function initPager(Request $request)
    {
        if (is_array($this->object)) {
            if (!isset($this->object['has_comments'])) {
                svDebug::_dx($this->object, 'toArray: return array_merge($r, $this->getCommentParams())');
            }
            $this->has_comments = $this->object['has_comments'];
        } else {
            $this->has_comments = $this->object && $this->object->hasComments();
        }
        if ($this->has_comments) {
            $max_per_page = vjComment::getMaxPerPage();
            $page = commentTools::getPage($this->object);

            $this->pager = new svDoctrinePager('Comment', $max_per_page);
            $this->pager->setQueryParams('by_record_id', array(
                'method' => 'createQueryComments',
                'record_id' => $this->object['id'],
                'order' => vjComment::getListOrder(),
                'model' => commentTools::getComponentName($this->object)
            ));
            $this->pager->setPage($page);
            $this->pager->init();
            $this->cpt = $max_per_page * ($page - 1);
        }
    }

    public function lastAction()
    {
        $manager = $this->getManager();
        return $this->render('BrotherCommentBundle:Comment:_last.html.twig', array(
            'objects' => $manager->getLast(5),
        ));
    }

}
