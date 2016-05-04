<?php

namespace Brother\CommentBundle\Controller;

use Brother\CommentBundle\Entity\CommentManager;
use Brother\CommonBundle\AppDebug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Brother\CommentBundle\Entity\Comment;
use Brother\CommentBundle\Form\CommentType;

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
    /**
     * Creates a new Comment entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Comment();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('comment_show', array('id' => $entity->getId())));
        }

        return $this->render('BrotherCommentBundle:Comment:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Comment entity.
     *
     * @param Comment $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Comment $entity)
    {
        $form = $this->createForm(new CommentType(), $entity, array(
            'action' => $this->generateUrl('comment_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Comment entity.
     *
     */
    public function newAction()
    {
        $entity = new Comment();
        $form   = $this->createCreateForm($entity);

        return $this->render('BrotherCommentBundle:Comment:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Comment entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BrotherCommentBundle:Comment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Comment entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BrotherCommentBundle:Comment:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Comment entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BrotherCommentBundle:Comment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Comment entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BrotherCommentBundle:Comment:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Comment entity.
    *
    * @param Comment $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Comment $entity)
    {
        $form = $this->createForm(new CommentType(), $entity, array(
            'action' => $this->generateUrl('comment_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Comment entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BrotherCommentBundle:Comment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Comment entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('comment_edit', array('id' => $id)));
        }

        return $this->render('BrotherCommentBundle:Comment:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Comment entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BrotherCommentBundle:Comment')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Comment entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('comment'));
    }

    /**
     * Creates a form to delete a Comment entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('comment_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
	public function executeReporting(Request $request)
	{
		$this->form = new CommentReportForm(null, array(
							 'id_comment' => $request->getParameter('id'),
							 'referer' => $request->getReferer() . "#" . $request->getParameter('num')
																							));

		if ($request->isMethod('post')) {
			$this->form->bind($request->getParameter($this->form->getName()));
			if ($this->form->isValid()) {
				$this->form->save();
				$this->redirect("@comment_reported");
			}
		}
	}

	public function executeReported(Request $request)
	{
	}

    public function executeFormComment(Request $request)
    {
        if ($this->object) {
            $id = is_array($this->object) ? $this->object['id'] : $this->object->getId();
            $this->form = new CommentForm(null, array('user' => $this->getUser(), 'name' => commentTools::generateCryptModel($this->object)));
            $this->form->setDefault('record_model', commentTools::getComponentName($this->object));
            $this->form->setDefault('record_id', $id);
            if ($request->isMethod('post')) {

                //preparing temporary array with sent values
                $formValues = $request->getParameter($this->form->getName());
                if (vjComment::isPostedForm($formValues, $this->form)) {
                    $this->form->bind($formValues);
                    if ($this->form->isValid()) {
                        $this->form->getObject()->setLink($_SERVER['HTTP_REFERER']);
                        $this->form->save();
                        $this->initPager($request);
                        $url = $this->generateNewUrl($request->getUri());
                        $this->getContext()->getController()->redirect($url, 0, 302);
                    }
                }
            }
        }
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


    private function generateNewUrl($uri)
    {
        if ($this->has_comments) {
            $page = $this->pager->getLastPage();
            if (vjComment::getListOrder() == "DESC") {
                $page = $this->pager->getFirstPage();
            }
        } else {
            $page = 1;
        }
        $url = commentTools::rewriteUrlForPage($uri, $page, commentTools::generateCryptModel($this->object), false);
        return $url . "#" . ($this->has_comments ? $this->pager->getNbResults() : 0);
    }

}
