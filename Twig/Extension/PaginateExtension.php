<?php

/*
 *
 * Fco Javier Aceituno
 * javier.aceituno@ideup.com
 * 
 * Luis Cordova cordoval@gmail.com
 * Symfony2 Developer
 */

namespace Ideap\SimplePaginatorBundle\Twig\Extension;

use Ideup\SimplePaginatorBundle\Paginator\Paginator as Paginator;


class PaginatorExtension extends \Twig_Extension {

  public function getFilters() {
    return array(
        'paginator' => new \Twig_Filter_Method($this, 'simple_paginate')
    );
  }

  public function simple_paginate(Paginator $paginator)
  {
    return $paginator->render();
  }

  public function getName() {
    return 'paginate_extension';
  }

}

