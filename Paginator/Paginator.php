<?php

namespace Ideup\SimplePaginatorBundle\Paginator;

use Symfony\Component\HttpFoundation\Request as Request;

use Doctrine\ORM\Query as Query;

class Paginator
{

  protected $page;

  protected $limit;

  protected $pages;

  protected $totalItems;

  protected $request;

  public function __construct(Request $request)
  {
    $this->request      = $request;    
    $this->page         = ($requestPage = $request->query->get('page')) ? $requestPage : 1;
    $this->limit        = ($requestLimit = $request->query->get('limit')) ? $requestLimit : 10;
  }

  public function transformQuery(Query &$query)
  {
    // TODO: Don't get results and then count. Only call doctrine method that internally count the results (look for)
    // The only way this can hold is if we work with Doctrine Collections \Doctrine\Common\Collections\Collection
    $this->totalItems   = $query->getResult()->count();
    $this->pages        = ceil($this->totalItems / $this->limit);

    $query->setMaxResults($this->limit);
    $query->setFirstResult(($this->page - 1) * $this->limit);

    return $query;
  }

  public function render()
  {
    $url = $this->request->getBaseUrl() . $this->request->getPathInfo();

    $strPaginator = 
      " <ul id='paginate_elements'>
          <li class='left'>
            <a href='$url?limit={$this->limit}&page=1'>First</a>
          </li>"
      ;

    if ($this->page != 1){
      $strPaginator .= 
        " <li class='previous'>
            <a href='$url?limit={$this->limit}&page={$this->getPrevious()}'>Previous</a>
          </li>"
        ;
    }

    $strPaginator .= "<li class='actual'>{$this->getDisplayedPagesFrom()} - {$this->getDisplayedPagesTo()}</li>";

    if ($this->page != $this->pages){
      $strPaginator .= 
        " <li class='next'>
            <a href='$url?limit={$this->limit}&page={$this->getNext()}'>Next</a>
          </li>"
        ;
    }

    $strPaginator .=
      " <li class='right'>
          <a href='$url?limit={$this->limit}&page={$this->pages}'>Last</a>
        </li>"
      ;
    $strPaginator .= "</ul>";

    return $strPaginator;
  }

  public function getNext()
  {
    return $this->page + 1;
  }

  public function getPrevious()
  {
    return $this->page - 1;
  }

  public function getDisplayedPagesFrom()
  {
    return (($this->page - 1) * $this->limit) + 1;
  }

  public function getDisplayedPagesTo()
  {
    $first = $this->getDisplayedPagesFrom();
    return (($first + $this->limit) > $this->totalItems) ? $this->totalItems : ($first + $this->limit) - 1;
  }

}
