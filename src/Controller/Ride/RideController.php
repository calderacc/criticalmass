<?php declare(strict_types=1);

namespace App\Controller\Ride;

use App\Entity\Ride;
use App\Criticalmass\SeoPage\SeoPage;
use App\Event\View\ViewEvent;
use function GuzzleHttp\Psr7\str;
use Sabre\VObject\Component\VCalendar;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Controller\AbstractController;
use App\Entity\Weather;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class RideController extends AbstractController
{
    public function listAction(): Response
    {
        $ridesResult = $this->getRideRepository()->findRidesInInterval();

        $rides = [];

        /** @var Ride $ride */
        foreach ($ridesResult as $ride) {
            $rides[$ride->getDateTime()->format('Y-m-d')][] = $ride;
        }

        return $this->render('Ride/list.html.twig', [
            'rides' => $rides,
        ]);
    }

    /**
     * @ParamConverter("ride", class="App:Ride")
     */
    public function showAction(Request $request, SeoPage $seoPage, EventDispatcherInterface $eventDispatcher, Ride $ride): Response
    {
        $nextRide = $this->getRideRepository()->getNextRide($ride);
        $previousRide = $this->getRideRepository()->getPreviousRide($ride);

        $eventDispatcher->dispatch(ViewEvent::NAME, new ViewEvent($ride));

        $seoPage
            ->setDescription('Informationen, Strecken und Fotos von der Critical Mass in ' . $ride->getCity()->getCity() . ' am ' . $ride->getDateTime()->format('d.m.Y'))
            ->setCanonicalForObject($ride);

        if ($ride->getImageName()) {
            $seoPage->setPreviewPhoto($ride);
        } elseif ($ride->getFeaturedPhoto()) {
            $seoPage->setPreviewPhoto($ride->getFeaturedPhoto());
        } else {
            $seoPage->setPreviewMap($ride);
        }

        if ($ride->getSocialDescription()) {
            $seoPage->setDescription($ride->getSocialDescription());
        } elseif ($ride->getDescription()) {
            $seoPage->setDescription($ride->getDescription());
        }

        /**
         * @var Weather $weather
         */
        $weather = $this->getWeatherRepository()->findCurrentWeatherForRide($ride);

        if ($weather) {
            $weatherForecast = round($weather->getTemperatureEvening()) . ' °C, ' . $weather->getWeatherDescription();
        } else {
            $weatherForecast = null;
        }

        if ($this->getUser()) {
            $participation = $this->getParticipationRepository()->findParticipationForUserAndRide($this->getUser(),
                $ride);
        } else {
            $participation = null;
        }

        return $this->render('Ride/show.html.twig', [
            'city' => $ride->getCity(),
            'ride' => $ride,
            'tracks' => $this->getTrackRepository()->findTracksByRide($ride),
            'photos' => $this->getPhotoRepository()->findPhotosByRide($ride),
            'subrides' => $this->getSubrideRepository()->getSubridesForRide($ride),
            'nextRide' => $nextRide,
            'previousRide' => $previousRide,
            'dateTime' => new \DateTime(),
            'weatherForecast' => $weatherForecast,
            'participation' => $participation,
        ]);
    }

    /**
     * @ParamConverter("ride", class="App:Ride")
     */
    public function icalAction(Ride $ride): Response
    {
        $vcalendar = new VCalendar([
            'VEVENT' => [
                'SUMMARY' => $ride->getTitle(),
                'DTSTART' => $ride->getDateTime(),
                'DTEND'   => $ride->getDateTime()->add(new \DateInterval('PT2H')),
            ],
        ]);

        $filename = sprintf('%s.ics', $ride->getTitle());
        
        $content = $vcalendar->serialize();

        $response = new Response($content);

        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', 'text/calendar');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '";');
        $response->headers->set('Content-length', strlen($content));

        return $response;
    }
}
