<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\City;
use App\Entity\CityCycle;
use App\Entity\Region;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;

class CycleController extends BaseController
{
    /**
     * @Operation(
     *     tags={"Cycles"},
     *     summary="Returns a list of city cycles",
     *     @SWG\Parameter(
     *         name="citySlug",
     *         in="query",
     *         description="Provide a city slug",
     *         required=false,
     *         @SWG\Schema(type="string"),
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="regionSlug",
     *         in="query",
     *         description="Provide a region slug",
     *         required=false,
     *         @SWG\Schema(type="string"),
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="validFrom",
     *         in="query",
     *         description="Only retrieve cycles valid after the provied date",
     *         required=false,
     *         @SWG\Schema(type="date"),
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="validUntil",
     *         in="query",
     *         description="Only retrieve cycles valid before the provied date",
     *         required=false,
     *         @SWG\Schema(type="date"),
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="validNow",
     *         in="query",
     *         description="Only retrieve cycles valid for the current month",
     *         required=false,
     *         @SWG\Schema(type="bool"),
     *         type="boolean"
     *     ),
     *     @SWG\Parameter(
     *         name="dayOfWeek",
     *         in="query",
     *         description="Limit the results to this day of week",
     *         required=false,
     *         @SWG\Schema(type="int"),
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="weekOfMonth",
     *         in="query",
     *         description="Limit the results to this week of month",
     *         required=false,
     *         @SWG\Schema(type="int"),
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @ParamConverter("city", class="App:City", isOptional=true)
     * @ParamConverter("region", class="App:Region", isOptional=true)
     * @ParamConverter("validFrom", class="DateTime", isOptional=true)
     * @ParamConverter("validUntil", class="DateTime", isOptional=true)
     * @Route("/cycles", name="caldera_criticalmass_rest_cycles_list", methods={"GET"}, options={"expose"=true})
     */
    public function listAction(Request $request, ManagerRegistry $managerRegistry, City $city = null, Region $region = null, \DateTime $validFrom = null, \DateTime $validUntil = null): Response
    {
        $validNow = $request->query->getBoolean('validNow', null);
        $dayOfWeek = $request->query->getInt('dayOfWeek', null);
        $weekOfMonth = $request->query->getInt('weekOfMonth', null);

        $cycleList = $managerRegistry->getRepository(CityCycle::class)->findForApi($city, $region, $validFrom, $validUntil, $validNow, $dayOfWeek, $weekOfMonth);

        $context = new Context();

        $view = View::create();
        $view
            ->setContext($context)
            ->setData($cycleList)
            ->setFormat('json')
            ->setStatusCode(200);

        return $this->handleView($view);
    }
}
