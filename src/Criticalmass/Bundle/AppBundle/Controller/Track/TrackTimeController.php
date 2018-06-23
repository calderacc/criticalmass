<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Controller\Track;

use Criticalmass\Bundle\AppBundle\Event\Track\TrackTimeEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Criticalmass\Bundle\AppBundle\Controller\AbstractController;
use Criticalmass\Bundle\AppBundle\Entity\Track;
use Criticalmass\Component\Gps\TrackTimeshift\TrackTimeshift;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackTimeController extends AbstractController
{
    /**
     * @Security("is_granted('edit', track)")
     * @ParamConverter("track", class="AppBundle:Track", options={"id" = "trackId"})
     */
    public function timeAction(Request $request, EventDispatcher $eventDispatcher, Track $track, TrackTimeshift $trackTimeshift): Response
    {
        $form = $this->createFormBuilder($track)
            ->setAction($this->generateUrl('caldera_criticalmass_track_time', [
                'trackId' => $track->getId()
            ]))
            ->add('startDate', DateType::class)
            ->add('startTime', TimeType::class)
            ->getForm();

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->timePostAction($request, $eventDispatcher, $track, $form, $trackTimeshift);
        } else {
            return $this->timeGetAction($request, $eventDispatcher, $track, $form, $trackTimeshift);
        }
    }

    protected function timeGetAction(Request $request, EventDispatcher $eventDispatcher, Track $track, FormInterface $form, TrackTimeshift $trackTimeshift): Response
    {
        return $this->render('AppBundle:Track:time.html.twig', [
            'form' => $form->createView(),
            'track' => $track,
        ]);
    }

    protected function timePostAction(Request $request, EventDispatcher $eventDispatcher, Track $track, FormInterface $form, TrackTimeshift $trackTimeshift): Response
    {
        // catch the old dateTime before it is overridden by the form submit
        $oldDateTime = $track->getStartDateTime();

        // now get the new values
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var Track $newTrack */
            $newTrack = $form->getData();

            $interval = $newTrack->getStartDateTime()->diff($oldDateTime);

            $trackTimeshift
                ->loadTrack($newTrack)
                ->shift($interval)
                ->saveTrack();

            $eventDispatcher->dispatch(TrackTimeEvent::NAME, new TrackTimeEvent($track));
        }

        return $this->redirect($this->generateUrl('caldera_criticalmass_track_list'));
    }
}
