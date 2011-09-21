<?php

namespace Ideup\SimplePaginatorBundle\Templating\Helper;

use 
    Symfony\Component\Templating\Helper\Helper,
    Symfony\Component\DependencyInjection\ContainerInterface,
    Ideup\SimplePaginatorBundle\Paginator\Paginator as Paginator
;

class PaginatorHelper extends Helper
{
    protected $paginator;
    protected $container;

    public function __construct(Paginator $paginator, ContainerInterface $container)
    {
        $this->paginator = $paginator;
        $this->container = $container;
    }

    public function getPaginator()
    {
        return $this->paginator;
    }

    public function render($route, $id = null, $options = array(), $view = null)
    {
        $view = (!is_null($view)) ? $view : 'IdeupSimplePaginatorBundle:Paginator:simple-paginator-list-view.html.twig';

        $defaultOptions = array(
            'container_class'       => 'simple_paginator',
            'paginator'             => $this->paginator,
            'id'                    => $id,
            'route'                 => $route,

            'previousPage'          => $this->paginator->getPreviousPage($id),
            'previosPageText'       => 'previous',
            'previousEnabledClass'  => 'left',
            'previousDisabledClass' => 'left_disabled',

            'minPage'               => $this->paginator->getMinPageInRange($id),
            'maxPage'               => $this->paginator->getMaxPageInRange($id),

            'currentPage'           => $this->paginator->getCurrentPage($id),
            'currentClass'          => 'current',

            'firstPage'             => $this->paginator->getFirstPage(),
            'lastPage'              => $this->paginator->getLastPage($id),

            'nextPage'              => $this->paginator->getNextPage($id),
            'nextPageText'          => 'next',
            'nextEnabledClass'      => 'right',
            'nextDisabledClass'     => 'right_disabled'            
        );

        $options = \array_merge($defaultOptions, $options);

        return $this->container->get('templating')->render($view, $options);
    }

    public function getName()
    {
        return 'simple_paginator_helper';
    }

}