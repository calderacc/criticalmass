<?php

namespace Caldera\Bundle\CalderaBundle\ViewStorage;

use Caldera\Bundle\CalderaBundle\EntityInterface\ViewableInterface;

interface ViewStorageInterface
{
    public function countView(ViewableInterface $viewable);
}