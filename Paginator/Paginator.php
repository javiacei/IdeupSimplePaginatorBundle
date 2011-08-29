<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

namespace Ideup\SimplePaginatorBundle\Paginator;

use Symfony\Component\HttpFoundation\Request as Request;
use Doctrine\ORM\Query as Query;
use DoctrineExtensions\Paginate\Paginate;

class Paginator
{
    /**
     * @var array $currentPage
     */
    protected $currentPage;
    /**
     * @var array $itemsPerPage
     */
    protected $itemsPerPage;
    /**
     * @var array $maxPagerItems
     */
    protected $maxPagerItems;
    /**
     * @var array $totalItems
     */
    protected $totalItems;

    /**
     * @var array $offset
     */
    protected $offset;

    /**
     * @param Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(Request $request)
    {
        $paginatorId = $request->query->get('paginatorId');
        $this->setFallbackValues();

        $page = (int)$request->query->get('page');
        $this->currentPage = array(
            md5($paginatorId) => ($page > 0) ? $page : $this->getFirstPage(),
        );

        // TODO: get the default values from config.yml
        $itemsPerPage = (int)$request->query->get('limit');
        $this->setItemsPerPage(($itemsPerPage > 0) ? $itemsPerPage : 10, $paginatorId);

        $this->setMaxPagerItems(10, $paginatorId);
        $this->totalItems = array(md5($paginatorId) => 0);
    }

    private function setFallbackValues()
    {
        $hash = md5(null);
        $this->currentPage[$hash]   = 0;
        $this->itemsPerPage[$hash]  = 10;
        $this->maxPagerItems[$hash] = 10;
        $this->totalItems[$hash]    = 0;
    }

    /**
     * @param string $id
     * @return int
     */
    public function getItemsPerPage($id = null)
    {
        $hash = md5($id);
        return isset($this->itemsPerPage[$hash]) ? $this->itemsPerPage[$hash] : $this->itemsPerPage[md5(null)];
    }

    /**
     * @param int $itemsPerPage
     * @param string $id
     */
    public function setItemsPerPage($itemsPerPage, $id = null)
    {
        $this->itemsPerPage[md5($id)] = (int)$itemsPerPage;
    }

    /**
     * @param string $id
     * @return int
     */
    public function getMaxPagerItems($id = null)
    {
        $hash = md5($id);
        return isset($this->maxPagerItems[$hash]) ?$this->maxPagerItems[$hash] : $this->maxPagerItems[md5(null)];
    }

    /**
     * @param int $maxPagerItems
     * @param string $id
     */
    public function setMaxPagerItems($maxPagerItems, $id = null)
    {
        $this->maxPagerItems[md5($id)] = (int)$maxPagerItems;
    }

    /**
     * @param int $offset
     * @param string $id
     */
    public function setOffset($offset, $id = null)
    {
        $this->offset[md5($id)] = (int)$offset;
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
        $offset = ($this->getCurrentPage($id) - 1) * $this->getItemsPerPage($id);
        $this->setOffset($offset, $id);
        return $query->setFirstResult($offset)->setMaxResults($this->getItemsPerPage($id));
    }

    /**
     * @param string $id
     * @return int
     */
    public function getCurrentPage($id = null)
    {
        $hash = md5($id);
        return isset($this->currentPage[$hash]) ? $this->currentPage[$hash] : $this->getFirstPage();
    }

    /**
     * Get the next page number
     *
     * @param string $id
     * @return int
     */
    public function getNextPage($id = null)
    {
        return $this->getCurrentPage($id) + 1;
    }

    /**
     * Get the previous page number
     *
     * @param string $id
     * @return int
     */
    public function getPreviousPage($id = null)
    {
        return $this->getCurrentPage($id) - 1;
    }

    /**
     * @return int
     */
    public function getMaxPageInRange($id = null)
    {
        $min = $this->getMinPageInRange($id);

        if ($min + $this->getMaxPagerItems($id) > $this->getLastPage($id)) {
          return $this->getLastPage($id);
        }

        return $min + $this->getMaxPagerItems($id);
    }

    /**
     * @param string $id
     * @return int
     */
    public function getMinPageInRange($id = null)
    {
        $maxItems = $this->getMaxPagerItems($id);
        return (int)floor($this->getCurrentPage($id)/$maxItems) * $maxItems + $this->getFirstPage();
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
        return (int)ceil($this->getTotalItems($id) / $this->getItemsPerPage($id));
    }

    /**
     * @return int
     */
    public function getFirstPage()
    {
        return 1;
    }

    /**
     * Get the initial offset of current page
     *
     * @param string $id
     * @return int
     */
    public function getOffset($id = null)
    {
        $hash = md5($id);
        return isset($this->offset[$hash]) ? $this->offset[$hash] : 0;
    }
}
