<?php declare(strict_types=1);

namespace Criticalmass\Component\Gps\GpxExporter;

use Doctrine\Bundle\DoctrineBundle\Registry;

/** @deprecated */
abstract class AbstractGpxExporter implements GpxExporterInterface
{
    /** @var Registry $registry */
    protected $registry;

    /** @var array $positionArray */
    protected $positionArray;

    /** @var string $gpxContent */
    protected $gpxContent = null;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function setPositionArray(array $positionArray): GpxExporterInterface
    {
        $this->positionArray = $positionArray;

        return $this;
    }

    public function execute(): GpxExporterInterface
    {
        if (count($this->positionArray) > 0) {
            $this->generateGpxContent();
        }

        return $this;
    }

    protected abstract function generateGpxContent(): GpxExporterInterface;

    public function getGpxContent(): string
    {
        return $this->gpxContent;
    }
} 