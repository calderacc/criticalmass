<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Controller;

use Caldera\Bundle\CalderaBundle\Entity\City;
use Caldera\Bundle\CalderaBundle\Entity\CitySlug;
use Caldera\Bundle\CalderaBundle\Entity\Region;
use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\StandardCityType;
use Malenki\Slug;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class CityManagementController extends AbstractController
{
    public function addAction(Request $request, $slug1, $slug2, $slug3)
    {
        /**
         * @var Region $region
         */
        $region = $this->getRegionRepository()->findOneBySlug($slug3);

        $city = new City();
        $city->setRegion($region);
        $city->setUser($this->getUser());

        $form = $this->createForm(
            new StandardCityType(),
            $city,
            [
                'action' => $this->generateUrl(
                    'caldera_criticalmass_desktop_city_add',
                    [
                        'slug1' => $slug1,
                        'slug2' => $slug2,
                        'slug3' => $slug3
                    ]
                )
            ]
        );

        if ('POST' == $request->getMethod()) {
            return $this->addPostAction($request, $city, $region, $form);
        } else {
            return $this->addGetAction($request, $city, $region, $form);
        }
    }

    protected function addGetAction(Request $request, City $city, Region $region, Form $form)
    {
        return $this->render(
            'CalderaCriticalmassCoreBundle:CityManagement:edit.html.twig',
            [
                'city' => null,
                'form' => $form->createView(),
                'hasErrors' => null,
                'country' => $region->getParent()->getName(),
                'state' => $region->getName(),
                'region' => $region
            ]
        );
    }

    protected function addPostAction(Request $request, City $city, Region $region, Form $form)
    {
        $form->handleRequest($request);

        $hasErrors = null;

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $citySlug = $this->createCitySlug($city);
            $city->addSlug($citySlug);

            $em->persist($citySlug);
            $em->persist($city);
            $em->flush();

            $hasErrors = false;

            $form = $this->createForm(
                StandardCityType::class,
                $city,
                [
                    'action' => $this->generateUrl(
                        'caldera_criticalmass_desktop_city_edit',
                        [
                            'citySlug' => $citySlug->getSlug()
                        ]
                    )
                ]
            );

            return $this->render(
                'CalderaCriticalmassCoreBundle:CityManagement:edit.html.twig',
                [
                    'city' => $city,
                    'form' => $form->createView(),
                    'hasErrors' => $hasErrors,
                    'country' => $region->getParent()->getName(),
                    'state' => $region->getName(),
                    'region' => $region
                ]
            );
        } elseif ($form->isSubmitted()) {
            $hasErrors = true;
        }

        return $this->render(
            'CalderaCriticalmassCoreBundle:CityManagement:edit.html.twig',
            [
                'city' => null,
                'form' => $form->createView(),
                'hasErrors' => $hasErrors,
                'country' => $region->getParent()->getName(),
                'state' => $region->getName(),
                'region' => $region
            ]
        );
    }

    public function editAction(Request $request, $citySlug)
    {
        $city = $this->getCityBySlug($citySlug);

        $form = $this->createForm(
            StandardCityType::class,
            $city,
            [
                'action' => $this->generateUrl(
                    'caldera_criticalmass_desktop_city_edit',
                    [
                        'citySlug' => $city->getMainSlugString()
                    ]
                )
            ]
        );

        if ('POST' == $request->getMethod()) {
            return $this->editPostAction($request, $city, $form);
        } else {
            return $this->editGetAction($request, $city, $form);
        }
    }

    protected function editGetAction(Request $request, City $city, Form $form)
    {
        return $this->render(
            'CalderaCriticalmassCoreBundle:CityManagement:edit.html.twig',
            [
                'city' => $city,
                'form' => $form->createView(),
                'hasErrors' => null,
                'country' => $city->getRegion()->getParent()->getName(),
                'state' => $city->getRegion()->getName(),
                'region' => $city->getRegion()
            ]
        );
    }

    protected function editPostAction(Request $request, City $city, Form $form)
    {
        $form->handleRequest($request);

        $hasErrors = null;

        if ($form->isValid()) {
            $archiveCity = $city->archive($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($city);
            $em->persist($archiveCity);
            $em->flush();

            $hasErrors = false;
        } elseif ($form->isSubmitted()) {
            $hasErrors = true;
        }

        return $this->render(
            'CalderaCriticalmassCoreBundle:CityManagement:edit.html.twig',
            [
                'city' => $city,
                'form' => $form->createView(),
                'hasErrors' => $hasErrors,
                'country' => $city->getRegion()->getParent()->getName(),
                'state' => $city->getRegion()->getName(),
                'region' => $city->getRegion()
            ]
        );
    }

    public function createCityFlowAction(Request $request, $slug1, $slug2, $slug3)
    {
        if ($this->container->has('profiler')) {
            $this->container->get('profiler')->disable();
        }

        /**
         * @var Region $region
         */
        $region = $this->getRegionRepository()->findOneBySlug($slug3);

        $city = new City();
        $city->setRegion($region);
        $city->setUser($this->getUser());

        $flow = $this->get('caldera.criticalmass.flow.create_city');
        $flow->bind($city);

        $form = $flow->createForm();

        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);

            if ($flow->nextStep()) {
                $form = $flow->createForm();
            } else {
                $em = $this->getDoctrine()->getManager();

                $citySlug = $this->createCitySlug($city);
                $city->addSlug($citySlug);

                $em->persist($citySlug);
                $em->persist($city);
                $em->flush();

                $flow->reset();

                return $this->redirectToObject($city);
            }
        }

        return $this->render('CalderaCriticalmassCoreBundle:CityManagement:create.html.twig', array(
            'form' => $form->createView(),
            'flow' => $flow,
            'city' => $city,
            'country' => $region->getParent()->getName(),
            'state' => $region->getName(),
            'region' => $region
        ));
    }

    protected function createCitySlug(City $city): CitySlug
    {
        $slugString = new Slug($city->getCity());

        $citySlug = new CitySlug();
        $citySlug
            ->setCity($city)
            ->setSlug($slugString)
        ;

        return $citySlug;
    }
}
