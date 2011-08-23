<?php

namespace Ideup\SimplePaginatorBundle\Twig\Extension;

use Ideup\SimplePaginatorBundle\Templating\Helper\PagebarHelper as PagebarHelper;
use Ideup\SimplePaginatorBundle\Paginator\Paginator as Paginator;


class PaginationExtension extends \Twig_Extension{
	
	protected $pagebar;	
	
	public function __construct(PagebarHelper $pagebar){
		$this->pagebar = $pagebar;
	}

	public function getFunctions() {
		return array(
			'pagination_first' => new \Twig_Function_Method($this, 'first', array('is_safe' => array('html'))),
			'pagination_prev' => new \Twig_Function_Method($this, 'prev', array('is_safe' => array('html'))),
			'pagination_numbers' => new \Twig_Function_Method($this, 'numbers', array('is_safe' => array('html'))),
			'pagination_next' => new \Twig_Function_Method($this, 'next', array('is_safe' => array('html'))),
			'pagination_last' => new \Twig_Function_Method($this, 'last', array('is_safe' => array('html'))),
			'pagination_counter' => new \Twig_Function_Method($this, 'counter', array('is_safe' => array('html'))),
			'pagination_offset' => new \Twig_Function_Method($this, 'offset', array('is_safe' => array('html'))),
			'pagination_offset0' => new \Twig_Function_Method($this, 'offset0', array('is_safe' => array('html')))			
		);
	}
	
	public function first($routeName=NULL, $title=NULL, $options=NULL, $paginatorId=NULL)
	{ 
		return $this->pagebar->first($routeName, $title, $options, $paginatorId);
	}

	public function prev($routeName=NULL, $title=NULL, $options=NULL, $paginatorId=NULL)
	{ 
		return $this->pagebar->prev($routeName, $title, $options, $paginatorId);
	}

	public function numbers($routeName=NULL, $options=NULL, $paginatorId=NULL)
	{
		return $this->pagebar->numbers($routeName, $options, $paginatorId);
	}
	
	public function next($routeName=NULL, $title=NULL, $options=NULL, $paginatorId=NULL)
	{
		return $this->pagebar->next($routeName, $title, $options, $paginatorId);
	}
	
	public function last($routeName=NULL, $title=NULL, $options=NULL, $paginatorId=NULL)
	{ 
		return $this->pagebar->last($routeName, $title, $options, $paginatorId);
	}
	
	public function counter($format=NULL, $paginatorId=NULL)
	{ 
		return $this->pagebar->counter($format, $paginatorId);
	}
	
	public function offset($index=NULL, $paginatorId=NULL)
	{ 
		return $this->pagebar->offset($index, $paginatorId, $startIndex=0);
	}
	
	public function offset0($index=NULL, $paginatorId=NULL)
	{ 
		return $this->pagebar->offset($index, $paginatorId, $startIndex=1);
	}

	public function getName() {
		return 'pagination_extension';
	}

}

