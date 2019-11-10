<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Factory;

use App\Criticalmass\DataQuery\Parameter\From;
use App\Criticalmass\DataQuery\Parameter\Size;
use Symfony\Component\HttpFoundation\Request;

class ParameterFactory implements ParameterFactoryInterface
{
    /** @var string $entityFqcn */
    protected $entityFqcn;

    public function setEntityFqcn(string $entityFqcn)
    {
        $this->entityFqcn = $entityFqcn;

        return $this;
    }

    public function createFromRequest(Request $request): array
    {
        $parameterList = [];

        if ($request->query->get('size')) {
            $size = (int)$request->query->get('size');

            $parameterList[] = new Size($size);
        }

        if ($request->query->get('from')) {
            $from = (int)$request->query->get('from');

            $parameterList[] = new From($from);
        }

        return $parameterList;
    }
}
