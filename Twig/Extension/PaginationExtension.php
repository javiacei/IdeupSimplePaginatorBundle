<?php

namespace Ideup\SimplePaginatorBundle\Twig\Extension;

use Ideup\SimplePaginatorBundle\Templating\Helper\PagebarHelper as PagebarHelper;
use Ideup\SimplePaginatorBundle\Paginator\Paginator as Paginator;

class PaginationExtension extends \Twig_Extension
{

    protected $pagebar;

    public function __construct(PagebarHelper $pagebar)
    {
        $this->pagebar = $pagebar;
    }

    public function getFunctions()
    {
        return array(
            'simple_paginator_render' => new \Twig_Function_Method($this, 'render', array('is_safe' => array('html'))),
            
        );
    }

    public function render($id, $route, $options = array(), $view = null)
    {
        return $this->pagebar->render($id, $route, $options, $view);
    }

    public function getName()
    {
        return 'pagination_extension';
    }

}

