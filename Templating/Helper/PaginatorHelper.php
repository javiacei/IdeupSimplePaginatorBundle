<?php

namespace Ideup\SimplePaginatorBundle\Templating\Helper;

use
    Doctrine\Common\Util\Debug,
    Symfony\Component\Templating\Helper\Helper,
    Symfony\Component\DependencyInjection\ContainerInterface
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
    protected $container;

    /**
     *  @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     *  @return Ideup\SimplePaginatorBundle\Paginator\Paginator
     */
    public function getPaginator()
    {
        return $this->container->get('ideup.simple_paginator');
    }

    /**
     *  @param string $route
     *  @param string $id
     *  @param array $options
     *  @param string $view
     *  @return string
     */
    public function render($route, $id = null, $options = array(), $view = null)
    {
        $view = (!is_null($view)) ? $view : 'IdeupSimplePaginatorBundle:Paginator:simple-paginator-list-view.html.twig';

        $defaultOptions = array(
            'container_class'       => 'simple_paginator',
            'id'                    => $id,
            'route'                 => $route,

            'previousPageText'      => 'previous',
            'previousEnabledClass'  => 'left',
            'previousDisabledClass' => 'left_disabled',

            'firstPageText'         => 'first',
            'firstEnabledClass'     => 'first',
            'firstDisabledClass'    => 'first_disabled',

            'firstPage'             => $this->getPaginator()->getFirstPage(),

            'previousPage'          => $this->getPaginator()->getPreviousPage($id),

            'minPage'               => $this->getPaginator()->getMinPageInRange($id),
            'maxPage'               => $this->getPaginator()->getMaxPageInRange($id),

            'currentPage'           => $this->getPaginator()->getCurrentPage($id),
            'currentClass'          => 'current',

            'nextPage'              => $this->getPaginator()->getNextPage($id),

            'lastPage'              => $this->getPaginator()->getLastPage($id),

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

    /**
     *  @return string
     */
    public function getName()
    {
        return 'simple_paginator_helper';
    }

}
