<?php

class COrderByQueryEncoder
{
    protected $_query;

    public function __construct($query)
    {
        $this->_query = $query;
    }

    /**
     * 将传入的orderBy数据，编码到query中并返回
     * @param $data 排序规则数据，可接受如下几种传入数据
     * $data = "created_at"; // 按created_at排序，正序
     * $data = ["created_at"]; // 按created_at排序，正序
     * $data = ["created_at"=>"asc"]; // 按created_at排序，正序
     * $data = ["created_at"=>"desc"]; // 按created_at排序，倒序
     * $data = ["category"=>"asc", "created_at"=>"desc"]; // 按category正向排序，在category相同的情况下按created_at排倒序
     *
     * @return Query
     */
    public function encode($data)
    {

    }
}