<?php
/**
 * Created by PhpStorm.
 * User: tigran
 * Date: 4/4/17
 * Time: 1:58 PM
 */

namespace AppBundle\Service;


use Symfony\Component\DependencyInjection\Container;

class AttributesDefinitionService
{
    /**
     * Symfony\Component\DependencyInjection\Container
     *
     * @var Container
     */
    private $container;
    private $settigs;

    public function __construct(Container $container, $settigs = [])
    {
        $this->container = $container;
        $this->settigs = $settigs;
    }


}