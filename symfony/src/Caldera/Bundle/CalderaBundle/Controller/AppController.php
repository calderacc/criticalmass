<?php

namespace Caldera\Bundle\CalderaBundle\Controller;

use Caldera\Bundle\CalderaBundle\Entity\App;
use Caldera\Bundle\CalderaBundle\Form\Type\AppType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AppController extends Controller
{
    public function listAction()
    {
        $apps = $this->getDoctrine()->getRepository('CalderaBundle:App')->findBy(array('user' => $this->getUser()->getId(), 'deleted' => 0));

        return $this->render('CalderaBundle:App:list.html.twig', array('apps' => $apps));
    }

    public function addAction(Request $request)
    {
        $app = new App();

        $form = $this->createForm(new AppType(), $app, array('action' => $this->generateUrl('caldera_criticalmass_api_app_add')));

        $form->handleRequest($request);

        $hasErrors = null;

        if ($form->isValid()) {
            $app->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($app);
            $em->flush();

            $hasErrors = false;

            $form = $this->createForm(new AppType(), $app, array('action' => $this->generateUrl('caldera_criticalmass_api_app_edit', array('appId' => $app->getId()))));
        } elseif ($form->isSubmitted()) {
            $hasErrors = true;
        }

        return $this->render('CalderaBundle:App:edit.html.twig', array('form' => $form->createView(), 'app2' => null, 'hasErrors' => $hasErrors));
    }

    public function editAction(Request $request, $appId)
    {
        $app = $this->getDoctrine()->getRepository('CalderaBundle:App')->find($appId);

        $form = $this->createForm(new AppType(), $app, array('action' => $this->generateUrl('caldera_criticalmass_api_app_edit', array('appId' => $app->getId()))));

        $form->handleRequest($request);

        $hasErrors = null;

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($app);
            $em->flush();

            $hasErrors = false;
        } elseif ($form->isSubmitted()) {
            $hasErrors = true;
        }

        return $this->render('CalderaBundle:App:edit.html.twig', array('form' => $form->createView(), 'app2' => $app, 'hasErrors' => $hasErrors));
    }

    public function deleteAction(Request $request, $appId)
    {
        $em = $this->getDoctrine()->getManager();

        $app = $em->find('CalderaBundle:App', $appId);

        if ($app && $app->getUser()->equals($this->getUser())) {
            $app->setDeleted(true);
            $em->persist($app);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('caldera_criticalmass_api_app_list'));
    }

    public function apiAction(Request $request)
    {
        return $this->render('CalderaBundle:App:api.html.twig');
    }
}
