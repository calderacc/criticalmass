<?php declare(strict_types=1);

namespace Criticalmass\Component\Cycles\Analyzer;

interface CycleAnalyzerModelFactoryInterface
{
    public function setRides(array $rides): CycleAnalyzerModelFactoryInterface;
    public function setSimulatedRides(array $simulatedRides): CycleAnalyzerModelFactoryInterface;
    public function build(): CycleAnalyzerModelFactoryInterface;
}
