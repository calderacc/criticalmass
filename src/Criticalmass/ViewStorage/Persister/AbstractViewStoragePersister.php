<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\Persister;

use App\Criticalmass\ViewStorage\ViewEntityFactory\ViewEntityFactoryInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

abstract class AbstractViewStoragePersister implements ViewStoragePersisterInterface
{
    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var SerializerInterface $serializer */
    protected $serializer;

    /** @var ViewEntityFactoryInterface $viewEntityFactory */
    protected $viewEntityFactory;

    public function __construct(RegistryInterface $registry, SerializerInterface $serializer, ViewEntityFactoryInterface $viewEntityFactory)
    {
        $this->registry = $registry;
        $this->serializer = $serializer;
        $this->viewEntityFactory = $viewEntityFactory;
    }
}
