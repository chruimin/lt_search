<?php

abstract class ABaseQueryEncoder implements ISearchQueryEncoder
{
    protected $_query;

    public function __construct($query)
    {
        $this->_query = $query;
    }

    abstract public function encode($key, $value);
}