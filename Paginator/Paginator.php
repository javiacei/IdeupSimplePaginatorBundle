<?php

namespace Ideup\SimplePaginatorBundle\Paginator;

use 
    Symfony\Component\HttpFoundation\Request
;

/**
 * Paginator
 *
 * @package IdeupSimplePaginatorBundle
 * @subpackage Paginator
 * @author Francisco Javier Aceituno <javier.aceituno@ideup.com>
 * @author Moises Maciá <moises.macia@ideup.com>
 */
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
     * @param Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(Request $request)
    {       
        $paginatorId = $request->get('paginatorId');
        $this->setFallbackValues();

        $page = (int)$request->get('page');
        
        $this->currentPage = array(
            md5($paginatorId) => ($page > 0) ? $page : $this->getFirstPage(),
        );

        $itemsPerPage = (int)$request->query->get('limit');
        $this->setItemsPerPage(($itemsPerPage > 0) ? $itemsPerPage : 10, $paginatorId);

        // TODO: MaxPagerItems load by config.yml
        $this->setMaxPagerItems(3, $paginatorId);
        $this->totalItems = array(md5($paginatorId) => 0);        
    }

    private function setFallbackValues()
    {
        $hash = md5(null);
        $this->currentPage[$hash]   = 0;
        $this->itemsPerPage[$hash]  = 10;
        $this->maxPagerItems[$hash] = 3;
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
        return $this;
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
        return $this;
    }
    
    protected function getAdapterOf($collection)
    {
        if (\is_array($collection)) {
            $className = 'Array';
        } else {
            $r = new \ReflectionClass($collection);
            $className = $r->getName();
        }
        
        $adapterName =
            __NAMESPACE__ . '\\Adapter\\' . $className . 'Adapter'
        ;

        return new $adapterName($collection);
    }

    /**
     * Transforms the given Doctrine DQL into a paginated query
     * If you need to paginate various queries in the same controller, you need to specify an $id
     *
     * @param mixed $collection
     * @param string $id
     * @return Doctrine\ORM\Query
     */
    public function paginate($collection, $id = null)
    {
        $adapter = $this->getAdapterOf($collection);

        $this->totalItems[md5($id)] = $adapter->getTotalResults();
        $offset = ($this->getCurrentPage($id) - 1) * $this->getItemsPerPage($id);

        return $adapter->setOffset($offset)->setLength($this->getItemsPerPage($id));
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

        return $min + $this->getMaxPagerItems($id) - 1;
    }

    /**
     * @param string $id
     * @return int
     */
    public function getMinPageInRange($id = null)
    {        
        $offset = floor(($this->getMaxPagerItems($id) - 1)/2);
        
        if ($this->getCurrentPage($id) > $offset) {
            return $this->getCurrentPage($id) - $offset;
        }

        return $this->getFirstPage();
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
}
