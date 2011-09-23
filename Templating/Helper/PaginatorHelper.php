<?php

namespace Ideup\SimplePaginatorBundle\Templating\Helper;

use
    Doctrine\Common\Util\Debug,
    Symfony\Component\Templating\Helper\Helper,
    Symfony\Component\DependencyInjection\ContainerInterface,
    Ideup\SimplePaginatorBundle\Paginator\Paginator as Paginator
;

/**
 * Paginator
 *
 * @package IdeupSimplePaginatorBundle
 * @subpackage Paginator
 * @author Francisco Javier Aceituno <javier.aceituno@ideup.com>
 * @author Gustavo Piltcher
 */
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
            'id'                    => $id,
            'route'                 => $route,
            
            'previosPageText'       => 'previous',
            'previousEnabledClass'  => 'left',
            'previousDisabledClass' => 'left_disabled',

            'firstPageText'         => 'first',
            'firstEnabledClass'     => 'first',
            'firstDisabledClass'    => 'first_disabled',
            
            'firstPage'             => $this->paginator->getFirstPage(),

            'previousPage'          => $this->paginator->getPreviousPage($id),

            'minPage'               => $this->paginator->getMinPageInRange($id),
            'maxPage'               => $this->paginator->getMaxPageInRange($id),

            'currentPage'           => $this->paginator->getCurrentPage($id),
            'currentClass'          => 'current',

            'firstPage'             => $this->paginator->getFirstPage(),
            'lastPage'              => $this->paginator->getLastPage($id),

            'nextPage'              => $this->paginator->getNextPage($id),

            'lastPage'              => $this->paginator->getLastPage($id),

            'lastPageText'          => 'last',
            'lastEnabledClass'      => 'last',
            'lastDisabledClass'     => 'last_disabled',
            
            'nextPageText'          => 'next',
            'nextEnabledClass'      => 'right',
            'nextDisabledClass'     => 'right_disabled',

            'routeParams'           => (\array_key_exists('route_params', $options) && is_array($options['route_params'])) ? $options['route_params'] : array()
        );

        $options = \array_merge($defaultOptions, $options);

        return $this->container->get('templating')->render($view, $options);
    }

    public function getName()
    {
        return 'simple_paginator_helper';
    }

}