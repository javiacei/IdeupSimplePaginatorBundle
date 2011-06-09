<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

namespace Ideup\SimplePaginatorBundle\Paginator;

use Symfony\Component\HttpFoundation\Request as Request;
use Doctrine\ORM\Query as Query;
use DoctrineExtensions\Paginate\Paginate;

class Paginator
{
    /**
     * @var int $currentPage
     */
    protected $currentPage;
    /**
     * @var int $itemsPerPage
     */
    protected $itemsPerPage;
    /**
     * @var int $maxPagerItems
     */
    protected $maxPagerItems;
    /**
     * @var array $totalItems
     */
    protected $totalItems;


    /**
     * @param Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(Request $request)
    {
        $page = (int)$request->query->get('page');
        $this->currentPage = ($page > 0) ? $page : 1;

        $itemsPerPage = (int)$request->query->get('limit');
        $this->itemsPerPage = ($itemsPerPage > 0) ? $itemsPerPage : 10; // TODO: get the default value from config.yml

        $this->maxPagerItems = 10;
        $this->totalItems = array(md5(null) => 0);
    }

    /**
     * Transforms the given Doctrine DQL into a paginated query
     * If you need to paginate various queries in the same controller, you need to specify an $id
     *
     * @param Doctrine\ORM\Query $query
     * @param string $id
     * @return Doctrine\ORM\Query
     */
    public function paginate(Query $query, $id = null)
    {
        $this->totalItems[md5($id)] = (int)Paginate::getTotalQueryResults($query);
        $offset = ($this->currentPage - 1) * $this->itemsPerPage;
        return $query->setFirstResult($offset)->setMaxResults($this->itemsPerPage);
    }

    /*public function render()
    {
        $url = $this->request->getBaseUrl() . $this->request->getPathInfo();

        $strPaginator =
            " <ul id='paginate_elements'>
            <li class='left'>
            <a href='$url?limit={$this->limit}&page=1'>Primera</a>
            </li>"
            ;

        if ($this->page != 1){
            $strPaginator .=
                " <li class='previous'>
                <a href='$url?limit={$this->limit}&page={$this->getPrevious()}'>Anterior</a>
                </li>"
                ;
        }else { // DISABLED
            $strPaginator .=
                " <li class='previous disabled'>
                <a>Anterior</a>
                </li>"
                ;
        }

        $strPaginator .= "<li class='actual'>Páginas: {$this->getDisplayedPagesFrom()} - {$this->getDisplayedPagesTo()}</li>";

        if ($this->page != $this->pages){
            $strPaginator .=
                " <li class='next'>
                <a href='$url?limit={$this->limit}&page={$this->getNext()}'>Siguiente</a>
                </li>"
                ;
        }else { // DISABLED
            $strPaginator .=
                " <li class='next disabled'>
                <a>Siguiente</a>
                </li>"
                ;
        }

        $strPaginator .=
            " <li class='right'>
            <a href='$url?limit={$this->limit}&page={$this->pages}'>Última</a>
            </li>"
            ;
        $strPaginator .= "</ul>";

        return $strPaginator;
    }*/

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Get the next page number
     *
     * @return int
     */
    public function getNextPage()
    {
        return $this->currentPage + 1;
    }

    /**
     * Get the previous page number
     *
     * @return int
     */
    public function getPreviousPage()
    {
        return $this->currentPage - 1;
    }

    /**
     * @return int
     */
    public function getMaxPageInRange($id = null)
    {
        $min = $this->getMinPageInRange();

        if ($min + $this->maxPagerItems > $this->getLastPage($id)) {
          return $this->getLastPage($id);
        }

        return $min + $this->maxPagerItems;
    }

    /**
     * @return int
     */
    public function getMinPageInRange()
    {
        return (int)floor($this->currentPage/$this->maxPagerItems) * $this->maxPagerItems + $this->getFirstPage();
    }

    /**
     * Get the total items in the non-paginated version of the query
     *
     * @param string $id
     * @return int
     */
    public function getTotalItems($id = null)
    {
        $hash = md5($id);
        return isset($this->totalItems[$hash]) ? $this->totalItems[$hash] : 0;
    }

    /**
     * Gets the last page number
     *
     * @param string $id
     * @return int
     */
    public function getLastPage($id = null)
    {
        return (int)ceil($this->getTotalItems($id) / $this->itemsPerPage);
    }

    /**
     * @return int
     */
    public function getFirstPage()
    {
        return 1;
    }
}
