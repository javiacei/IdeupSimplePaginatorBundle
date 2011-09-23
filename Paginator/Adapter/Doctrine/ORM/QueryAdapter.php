<?php

namespace Ideup\SimplePaginatorBundle\Paginator\Adapter\Doctrine\ORM;

use
    Doctrine\ORM\Query,
    DoctrineExtensions\Paginate\Paginate,

    Ideup\SimplePaginatorBundle\Paginator\Adapter\AdapterInterface
;


/**
 * QueryAdapter
 *
 * @package IdeupSimplePaginatorBundle
 * @subpackage Adapter
 * @author Francisco Javier Aceituno <javier.aceituno@ideup.com>

 */
class QueryAdapter implements AdapterInterface
{
    protected $query;

    /**
     * Constructor
     * 
     * @param Doctrine\ORM\Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * {@inheritdoc }
     */
    public function getTotalResults()
    {
        return (int)Paginate::getTotalQueryResults($this->query);
    }

    /**
     * {@inheritdoc }
     */
    public function setOffset($offset)
    {
        $this->query->setFirstResult($offset);
        return $this;
    }

    /**
     * {@inheritdoc }
     */
    public function setLength($length)
    {
        $this->query->setMaxResults($length);
        return $this;
    }

    /**
     * {@inheritdoc }
     */
    public function getResult()
    {
        return $this->query->getResult();
    }
}
