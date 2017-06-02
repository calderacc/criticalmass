<?php

namespace AppBundle\Controller\Track;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Ride;
use AppBundle\Entity\Track;
use AppBundle\Traits\TrackHandlingTrait;
use AppBundle\Gps\TrackTimeShift\TrackTimeShift;
use AppBundle\UploadValidator\TrackValidator;
use AppBundle\UploadValidator\UploadValidatorException\TrackValidatorException\TrackValidatorException;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;

class TrackManagementController extends AbstractController
{
    use TrackHandlingTrait;

    public function listAction()
    {
        /**
         * @var array Track
         */
        $tracks = $this->getTrackRepository()->findBy(
            [
                'user' => $this->getUser()->getId(),
                'deleted' => false
            ],
            [
                'startDateTime' => 'DESC'
            ]
        );

        return $this->render('AppBundle:Track:list.html.twig',
            array(
                'tracks' => $tracks
            )
        );
    }

    public function downloadAction(Request $request, UserInterface $user, int $trackId): Response
    {
        $track = $this->getCredentialsCheckedTrack($user, $trackId);

        $trackContent = file_get_contents($this->getTrackFilename($track));

        $response = new Response();

        $response->headers->add([
            'Content-disposition' => 'attachment; filename=track.gpx',
            'Content-type', 'text/plain'
        ]);

        $response->setContent($trackContent);

        return $response;
    }

    public function toggleAction(Request $request, UserInterface $user, int $trackId): Response
    {
        $track = $this->getCredentialsCheckedTrack($user, $trackId);

        $track->setEnabled(!$track->getEnabled());

        $this->getManager()->flush();

        $this->get('caldera.criticalmass.statistic.rideestimate.track')->calculateEstimates($track->getRide());

        return $this->redirect($this->generateUrl('caldera_criticalmass_track_list'));
    }

    public function deleteAction(Request $request, UserInterface $user, int $trackId): Response
    {
        $track = $this->getCredentialsCheckedTrack($user, $trackId);

        $track->setDeleted(true);

        $this->getManager()->flush();

        $this->get('caldera.criticalmass.statistic.rideestimate.track')->calculateEstimates($track->getRide());

        return $this->redirect($this->generateUrl('caldera_criticalmass_track_list'));
    }

    public function rangeAction(Request $request, $trackId)
    {
        /** @var Track $track */
        $track = $this->getTrackRepository()->findOneById($trackId);

        $form = $this->createFormBuilder($track)
            ->setAction($this->generateUrl('caldera_criticalmass_track_range',
                [
                    'trackId' => $track->getId()
                ]
            ))
            ->add('startPoint', HiddenType::class)
            ->add('endPoint', HiddenType::class)
            ->getForm();

        if ('POST' == $request->getMethod()) {
            return $this->rangePostAction($request, $track, $form);
        } else {
            return $this->rangeGetAction($request, $track, $form);
        }
    }

    protected function rangeGetAction(Request $request, Track $track, Form $form)
    {
        $llag = $this->container->get('caldera.criticalmass.gps.latlnglistgenerator.simple');
        $llag->loadTrack($track);
        $llag->execute();

        return $this->render('AppBundle:Track:range.html.twig',
            [
                'form' => $form->createView(),
                'track' => $track,
                'latLngList' => $llag->getList(),
                'gapWidth' => $this->getParameter('track.gap_width')
            ]
        );
    }

    protected function rangePostAction(Request $request, Track $track, Form $form)
    {
        $form->handleRequest($request);

        if ($form->isValid() && $track && $track->getUser()->equals($this->getUser())) {
            /**
             * @var Track $track
             */
            $track = $form->getData();

            $this->generatePolyline($track);
            $this->saveLatLngList($track);
            $this->updateTrackProperties($track);
            $this->calculateRideEstimates($track);
        }

        return $this->redirect($this->generateUrl('caldera_criticalmass_track_list'));
    }

    public function timeAction(Request $request, $trackId)
    {
        $track = $this->getTrackRepository()->findOneById($trackId);

        $form = $this->createFormBuilder($track)
            ->setAction($this->generateUrl(
                'caldera_criticalmass_track_time',
                [
                    'trackId' => $track->getId()
                ]
            ))
            ->add('startDate', DateType::class)
            ->add('startTime', TimeType::class)
            ->getForm();

        if ('POST' == $request->getMethod()) {
            return $this->timePostAction($request, $track, $form);
        } else {
            return $this->timeGetAction($request, $track, $form);
        }
    }

    protected function timeGetAction(Request $request, Track $track, Form $form)
    {
        return $this->render('AppBundle:Track:time.html.twig',
            [
                'form' => $form->createView(),
                'track' => $track,
            ]
        );
    }

    protected function timePostAction(Request $request, Track $track, Form $form)
    {
        // catch the old dateTime before it is overridden by the form submit
        $oldDateTime = $track->getStartDateTime();

        // now get the new values
        $form->handleRequest($request);

        if ($form->isValid() && $track && $track->getUser()->equals($this->getUser())) {
            /**
             * @var Track $newTrack
             */
            $newTrack = $form->getData();

            $interval = $newTrack->getStartDateTime()->diff($oldDateTime);

            /**
             * @var TrackTimeShift $tts
             */
            $tts = $this->get('caldera.criticalmass.gps.timeshift.track');

            $tts->loadTrack($newTrack)->shift($interval)->saveTrack();

            $this->updateTrackProperties($track);
        }

        return $this->redirect($this->generateUrl('caldera_criticalmass_track_list'));
    }

    public function drawAction(Request $request, $citySlug, $rideDate)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        if ('POST' == $request->getMethod()) {
            return $this->drawPostAction($request, $ride);
        } else {
            return $this->drawGetAction($request, $ride);
        }
    }

    protected function drawGetAction(Request $request, Ride $ride)
    {
        return $this->render(
            'AppBundle:Track:draw.html.twig',
            [
                'ride' => $ride
            ]
        );
    }

    protected function drawPostAction(Request $request, Ride $ride)
    {
        $polyline = $request->request->get('polyline');
        $geojson = $request->request->get('geojson');

        $track = new Track();

        $track->setCreationDateTime(new \DateTime());
        $track->setPolyline($polyline);
        $track->setGeoJson($geojson);
        $track->setRide($ride);
        $track->setSource(Track::TRACK_SOURCE_DRAW);
        $track->setUser($this->getUser());
        $track->setUsername($this->getUser()->getUsername());
        $track->setTrackFilename('foo');

        $em = $this->getDoctrine()->getManager();
        $em->persist($track);
        $em->flush();

        return $this->redirectToRoute('caldera_criticalmass_track_list');
    }

    public function editAction(Request $request, int $trackId)
    {
        /** @var Track $track */
        $track = $this->getTrackRepository()->find($trackId);
        $ride = $track->getRide();

        if ($track->getUser() != $track->getUser()) {
            return $this->createAccessDeniedException();
        }

        if ('POST' == $request->getMethod()) {
            return $this->editPostAction($request, $ride, $track);
        } else {
            return $this->editGetAction($request, $ride, $track);
        }
    }

    protected function editGetAction(Request $request, Ride $ride, Track $track)
    {
        return $this->render(
            'AppBundle:Track:draw.html.twig',
            [
                'ride' => $ride,
                'track' => $track
            ]
        );
    }

    protected function editPostAction(Request $request, Ride $ride, Track $track)
    {
        $polyline = $request->request->get('polyline');
        $geojson = $request->request->get('geojson');

        $track->setPolyline($polyline);
        $track->setGeoJson($geojson);

        $em = $this->getDoctrine()->getManager();
        $em->persist($track);
        $em->flush();

        return $this->redirectToRoute('caldera_criticalmass_track_list');
    }

    protected function getCredentialsCheckedTrack(UserInterface $user, int $trackId): Track
    {
        /**
         * @var Track $track
         */
        $track = $this->getTrackRepository()->find($trackId);

        if (!$track) {
            throw $this->createNotFoundException();
        }

        if ($track->getUser() !== $user) {
            throw $this->createAccessDeniedException();
        }

        return $track;
    }

    protected function getTrackFilename(Track $track): string
    {
        $rootDirectory = $this->getParameter('kernel.root_dir');
        $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
        $filename = $helper->asset($track, 'trackFile');

        return $rootDirectory . '/../web' . $filename;
    }
}
