<?php

namespace Ideup\SimplePaginatorBundle\Paginator\Adapter\Doctrine\ORM;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Ideup\SimplePaginatorBundle\Paginator\Adapter\AdapterInterface;

/**
 * QueryAdapter
 *
 * @package IdeupSimplePaginatorBundle
 * @subpackage Adapter
 * @author Francisco Javier Aceituno <javier.aceituno@ideup.com>

 */
class QueryAdapter implements AdapterInterface {

    protected $query;

    /**
     * Constructor
     * 
     * @param Doctrine\ORM\Query $query
     */
    public function __construct(Query $query) {
        $this->query = $query;
    }

    /**
     * {@inheritdoc }
     */
    public function getTotalResults() {
        $paginate = new Paginator($this->query);
        $paginate->count();
        return (int) $paginate->count();
//        return (int)Paginate::getTotalQueryResults($this->query);
    }

    /**
     * {@inheritdoc }
     */
    public function setOffset($offset) {
        $this->query->setFirstResult($offset);
        return $this;
    }

    /**
     * {@inheritdoc }
     */
    public function setLength($length) {
        $this->query->setMaxResults($length);
        return $this;
    }

    /**
     * {@inheritdoc }
     */
    public function getResult($hidrationMode = null) {
        return $this->query->getResult($hidrationMode);
    }

}
