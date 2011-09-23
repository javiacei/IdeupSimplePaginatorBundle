<?php

namespace Ideup\SimplePaginatorBundle\Paginator\Adapter;

/**
 * AdapterInterface
 *
 * @package IdeupSimplePaginatorBundle
 * @subpackage Adapter
 * @author Francisco Javier Aceituno <javier.aceituno@ideup.com>
 */
interface AdapterInterface
{
   /**
     * @return integer. Returns the total number of elements
     */
    public function getTotalResults();

    /**
     * This method sets the first element to display
     *
     * @param integer $offset
     * @return AdapterInterface. Returns itself
     */
    public function setOffset($offset);

    /**
     * This method sets the maximum number of elements
     * 
     * @param integer $lenght
     * @return AdapterInterface. Returns itself
     */
    public function setLength($length);

    /**
     * @return array. Returns the collection of paged objects
     */
    public function getResult();
}
