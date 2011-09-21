<?php

namespace Ideup\SimplePaginatorBundle\Templating\Helper;

use 
    Symfony\Component\Templating\Helper\Helper,
    Symfony\Component\DependencyInjection\ContainerInterface,
    Ideup\SimplePaginatorBundle\Paginator\Paginator as Paginator
;

class PagebarHelper extends Helper
{

    protected $paginator;
    protected $container;

    public function __construct(Paginator $paginator, ContainerInterface $container)
    {
        $this->paginator = $paginator;
        $this->container = $container;
    }

    public function render($id, $route, $options = array(), $view = null)
    {
        $view = (!is_null($view)) ? $view : 'IdeupSimplePaginatorBundle:Paginator:paginator.html.twig';

        $defaultOptions = array(
            'paginator' => $this->paginator,
            'id' => $id,
            'route' => $route
        );

        $options = \array_merge($defaultOptions, $options);

        return $this->container->get('templating')->render($view, $options);
    }

    public function getName()
    {
        return 'pagination_helper';
    }

}