<?php declare(strict_types=1);

namespace App\Controller\Api;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class BaseController extends AbstractFOSRestController
{
    protected function getDeserializationContext(): DeserializationContext
    {
        $deserializationContext = $this->initSerializerContext(new DeserializationContext());

        return $deserializationContext;
    }

    protected function getSerializationContext(): SerializationContext
    {
        $serializationContext = $this->initSerializerContext(new SerializationContext());

        return $serializationContext;
    }

    protected function initSerializerContext(Context $context): Context
    {
        $context->setSerializeNull(true);

        return $context;
    }

    protected function deserializeRequest(Request $request, SerializerInterface $serializer, string $modelClass)
    {
        $content = null;

        if ($request->isMethod(Request::METHOD_GET)) {
            $content = $request->getQueryString();
        } else {
            $content = $request->getContent();
        }

        return $serializer->deserialize($content, $modelClass, 'json');
    }
}
