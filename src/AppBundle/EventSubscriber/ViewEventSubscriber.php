<?php declare(strict_types=1);

namespace AppBundle\EventSubscriber;

use AppBundle\Criticalmass\ViewStorage\ViewStorageCacheInterface;
use AppBundle\Event\View\ViewEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ViewEventSubscriber implements EventSubscriberInterface
{
    /** @var ViewStorageCacheInterface $viewStorageCache */
    protected $viewStorageCache;

    public function __construct(ViewStorageCacheInterface $viewStorageCache)
    {
        $this->viewStorageCache = $viewStorageCache;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ViewEvent::NAME => 'onView',
        ];
    }

    public function onView(ViewEvent $viewEvent): void
    {
        $this->viewStorageCache->countView($viewEvent->getViewable());
    }
}
