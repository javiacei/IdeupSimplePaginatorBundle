<?php

namespace Ideup\SimplePaginatorBundle\Paginator\Adapter;

use
    Ideup\SimplePaginatorBundle\Paginator\Adapter\AdapterInterface
;

/**
 * ArrayAdapter
 *
 * @package IdeupSimplePaginatorBundle
 * @subpackage Adapter
 * @author Francisco Javier Aceituno <javier.aceituno@ideup.com>

 */
class ArrayAdapter implements AdapterInterface
{    
    protected $collection;
    protected $offset;
    protected $length;

    public function __construct(array $collection)
    {
        $this->collection = $collection;
    }

    /**
     * {@inheritdoc }
     */
    public function getTotalResults()
    {
        return count($this->collection);
    }

    /**
     * {@inheritdoc }
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * {@inheritdoc }
     */
    public function setLength($length)
    {
        $this->length = $length;
        return $this;
    }

    /**
     * {@inheritdoc }
     */
    public function getResult()
    {
        return \array_slice($this->collection, $this->offset, $this->length);
    }
}