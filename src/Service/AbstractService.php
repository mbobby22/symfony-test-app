<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

abstract class AbstractService implements ServiceInterface
{
    /** @var ParameterBagInterface $params */
    protected ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }
}
