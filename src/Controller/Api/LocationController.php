<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\City;
use App\Entity\Location;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LocationController extends BaseController
{
    /**
     * @Operation(
     *     tags={"Location"},
     *     summary="Retrieve a list of locations of a city",
     *     @SWG\Parameter(
     *         name="citySlug",
     *         in="path",
     *         description="Slug of the city",
     *         required=true,
     *         @SWG\Schema(type="string"),
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @ParamConverter("city", class="App:City")
     * @Route("/{citySlug}/location", name="caldera_criticalmass_rest_location_list", methods={"GET"}, options={"expose"=true})
     */
    public function listLocationAction(ManagerRegistry $registry, City $city): Response
    {
        $locationList = $registry->getRepository(Location::class)->findLocationsByCity($city);

        $view = View::create();
        $view
            ->setData($locationList)
            ->setFormat('json')
            ->setStatusCode(Response::HTTP_OK);

        return $this->handleView($view);
    }

    /**
     * Show details of a specified location.
     *
     * @Operation(
     *     tags={"Location"},
     *     summary="Show details of a location",
     *     @SWG\Parameter(
     *         name="citySlug",
     *         in="path",
     *         description="Slug of the city",
     *         required=true,
     *         @SWG\Schema(type="string"),
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="locationSlug",
     *         in="path",
     *         description="Slug of the location",
     *         required=true,
     *         @SWG\Schema(type="string"),
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @ParamConverter("location", class="App:Location")
     * @Route("/{citySlug}/location/{locationSlug}", name="caldera_criticalmass_rest_location_show", methods={"GET"}, options={"expose"=true})
     */
    public function showLocationAction(Location $location): Response
    {
        $context = new Context();

        $view = View::create();
        $view
            ->setData($location)
            ->setFormat('json')
            ->setStatusCode(Response::HTTP_OK)
            ->setContext($context);

        return $this->handleView($view);
    }
}
