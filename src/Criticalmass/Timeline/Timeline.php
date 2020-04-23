<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline;

use App\Criticalmass\Timeline\Collector\AbstractTimelineCollector;
use App\Criticalmass\Timeline\Collector\TimelineCollectorInterface;
use App\Criticalmass\Timeline\Item\ItemInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Persistence\ManagerRegistry;
use Flagception\Manager\FeatureManagerInterface;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\Templating\EngineInterface;

class Timeline implements TimelineInterface
{
    /** @var Registry $doctrine */
    protected $doctrine;

    /** @var TwigEngine $templating */
    protected $templating;

    /** @var array $collectorList */
    protected $collectorList = [];

    /** @var array $items */
    protected $items = [];

    /** @var array $contentList */
    protected $contentList = [];

    /** @var \DateTime $startDateTime */
    protected $startDateTime = null;

    /** @var \DateTime $endDateTime */
    protected $endDateTime = null;

    /** @var FeatureManagerInterface $featureManager */
    protected $featureManager;

    public function __construct(ManagerRegistry $doctrine, EngineInterface $templating, FeatureManagerInterface $featureManager)
    {
        $this->doctrine = $doctrine;
        $this->templating = $templating;
        $this->featureManager = $featureManager;
    }

    public function addCollector(AbstractTimelineCollector $collector): TimelineInterface
    {
        if ($this->checkFeatureStatusForCollector($collector)) {
            array_push($this->collectorList, $collector);
        }

        return $this;
    }

    public function setDateRange(\DateTime $startDateTime, \DateTime $endDateTime): TimelineInterface
    {
        $this->startDateTime = $startDateTime;
        $this->endDateTime = $endDateTime;

        return $this;
    }

    public function execute(): TimelineInterface
    {
        $this->process();

        return $this;
    }

    protected function process(): Timeline
    {
        /** @var AbstractTimelineCollector $collector */
        foreach ($this->collectorList as $collector) {
            $collector->setDateRange($this->startDateTime, $this->endDateTime);

            $collector->execute();

            $this->items = array_merge($this->items, $collector->getItems());
        }

        krsort($this->items);

        $this->paginate();

        $this->createContent();

        return $this;
    }

    protected function paginate(): Timeline
    {
        $lastDateTime = new \DateTime();
        $threeMonthDateInterval = new \DateInterval('P3M');
        $lastDateTime->sub($threeMonthDateInterval);

        $maxItems = 50;
        $counter = 0;

        foreach ($this->items as $key => $item) {
            ++$counter;

            if ($item->getDateTime() < $lastDateTime || $counter > $maxItems) {
                unset($this->items[$key]);
            }
        }

        return $this;
    }

    protected function createContent(): Timeline
    {
        /** @var ItemInterface $item */
        foreach ($this->items as $item) {
            $templateName = $this->templateNameForItem($item);

            if (!array_key_exists($item->getTabName(), $this->contentList)) {
                $this->contentList[$item->getTabName()] = [];
            }

            $this->contentList[$item->getTabName()][]= $this->templating->render('Timeline/Items/' . $templateName . '.html.twig', [
                'item' => $item
            ]);
        }

        return $this;
    }

    protected function templateNameForItem(ItemInterface $item): string
    {
        $itemFullClassName = get_class($item);

        $itemClassNamespaces = explode('\\', $itemFullClassName);

        $itemClassName = array_pop($itemClassNamespaces);

        $templateName = lcfirst(str_replace('Item', '', $itemClassName));

        return $templateName;
    }

    public function getTimelineContentList(): array
    {
        return $this->contentList;
    }

    protected function checkFeatureStatusForCollector(TimelineCollectorInterface $timelineCollector): bool
    {
        $requiredFeatures = $timelineCollector->getRequiredFeatures();

        if (count($requiredFeatures) > 0) {
            foreach ($requiredFeatures as $requiredFeature) {
                if (!$this->featureManager->isActive($requiredFeature)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function getTimelineContent(): string
    {
        return join("\n", $this->getTimelineContentList());
    }
}

