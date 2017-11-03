<?php

namespace Criticalmass\Bundle\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\City;
use AppBundle\Entity\Post;
use AppBundle\Entity\Thread;
use AppBundle\EntityInterface\BoardInterface;
use AppBundle\Traits\ViewStorageTrait;
use Malenki\Slug;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class BoardController extends AbstractController
{
    use ViewStorageTrait;

    public function overviewAction(Request $request)
    {
        $boards = $this->getBoardRepository()->findEnabledBoards();

        $cities = $this->getCityRepository()->findCitiesWithBoard();

        return $this->render(
            'AppBundle:Board:overview.html.twig',
            [
                'boards' => $boards,
                'cities' => $cities
            ]
        );
    }

    public function listthreadsAction(Request $request, $boardSlug = null, $citySlug = null)
    {
        $board = null;
        $city = null;
        $threads = [];
        $newThreadUrl = '';

        if ($boardSlug) {
            $board = $this->getBoardRepository()->findBoardBySlug($boardSlug);

            $threads = $this->getThreadRepository()->findThreadsForBoard($board);

            $newThreadUrl = $this->generateUrl(
                'caldera_criticalmass_board_addthread',
                [
                    'boardSlug' => $board->getSlug()
                ]
            );
        }

        if ($citySlug) {
            $city = $this->getCheckedCity($citySlug);

            $threads = $this->getThreadRepository()->findThreadsForCity($city);

            $newThreadUrl = $this->generateUrl(
                'caldera_criticalmass_board_addcitythread',
                [
                    'citySlug' => $city->getSlug()
                ]
            );
        }

        return $this->render(
            'AppBundle:Board:list_threads.html.twig',
            [
                'threads' => $threads,
                'board' => ($board ? $board : $city),
                'newThreadUrl' => $newThreadUrl
            ]
        );
    }

    public function viewthreadAction(Request $request, $boardSlug = null, $citySlug = null, $threadSlug)
    {
        /**
         * @var BoardInterface $board
         */
        $board = null;

        if ($boardSlug) {
            $board = $this->getBoardRepository()->findBoardBySlug($boardSlug);
        }

        if ($citySlug) {
            $board = $this->getCheckedCity($citySlug);
        }

        $thread = $this->getThreadRepository()->findThreadBySlug($threadSlug);
        $posts = $this->getPostRepository()->findPostsForThread($thread);

        $this->countThreadView($thread);

        return $this->render(
            'AppBundle:Board:view_thread.html.twig',
            [
                'board' => $board,
                'thread' => $thread,
                'posts' => $posts
            ]
        );
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function addthreadAction(Request $request, $boardSlug = null, $citySlug = null)
    {
        /**
         * @var BoardInterface $board
         */
        $board = null;

        if ($boardSlug) {
            $board = $this->getBoardRepository()->findBoardBySlug($boardSlug);
        }

        if ($citySlug) {
            $board = $this->getCheckedCity($citySlug);
        }

        $data = [];
        $form = $this->createFormBuilder($data)
            ->add('title', TextType::class)
            ->add('message', TextareaType::class)
            ->getForm();

        if ('POST' == $request->getMethod()) {
            return $this->addThreadPostAction($request, $board, $form);
        } else {
            return $this->addThreadGetAction($request, $board, $form);
        }
    }

    protected function addThreadGetAction(Request $request, BoardInterface $board, Form $form)
    {
        return $this->render(
            'AppBundle:Board:add_thread.html.twig',
            [
                'board' => $board,
                'form' => $form->createView()
            ]
        );
    }

    protected function addThreadPostAction(Request $request, BoardInterface $board, Form $form)
    {
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            $thread = new Thread();
            $post = new Post();

            $slug = new Slug($data['title']);

            /* Okay, this is _really_ ugly */
            if ($board instanceof City) {
                $thread->setCity($board);
            } else {
                $thread->setBoard($board);
            }

            $thread->setTitle($data['title']);
            $thread->setFirstPost($post);
            $thread->setLastPost($post);
            $thread->setSlug($slug);

            $board->setLastThread($thread);
            $board->incPostNumber();
            $board->incThreadNumber();

            $post->setUser($this->getUser());
            $post->setMessage($data['message']);
            $post->setThread($thread);
            $post->setDateTime(new \DateTime());

            $em = $this->getDoctrine()->getManager();

            $em->persist($post);
            $em->persist($thread);
            $em->persist($board);

            $em->flush();

            return $this->redirectToObject($thread);
        }

        return $this->render(
            'AppBundle:Board:add_thread.html.twig',
            [
                'board' => $board,
                'form' => $form->createView()
            ]
        );
    }
}
