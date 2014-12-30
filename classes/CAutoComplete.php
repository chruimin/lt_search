<?php

class CAutoComplete
{
    /////////////////////////////////////// 测试代码（由于model还没有，所以需要等model搭建完成之后才能进行测试） /////////////////////////////////
    // return (new CAutoComplete)->executeByModel(new User(), ['first_name'=>Input::get('query')], 'first_name');
    // 或者
    // $user = new User();
    // return (new CAutoComplete)->execute($user->newQuery(), $user->enableOnSearch, ['first_name'=>Input::get('query')], 'first_name');
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * 使用方法
     * $query = (new CAutoComplete)->execute($query, $enableOnSearch, ['username'=>Input::get('query')], 'username');
     *
     * @param Query $query 传入Query
     * @param array $enableOnSearch 搜索配置
     * @param array $data Input数据，或者调用者构造的数组
     * @param string $valueField value字段
     * @param int $pageSize 返回结果的个数
     * @param string $primaryKey primary_key字段名称
     *
     * @return Query 组装后的query
     * @author 
     **/
    public function execute($query, $enableOnSearch, $data, $valueField, $pageSize=5, $primaryKey='id')
    {
        if(empty($data[$valueField])) {
            throw new ValueFieldNotFoundException();
        }

        $query = $this->build($query, $enableOnSearch, $data, [$valueField, $primaryKey], true, $primaryKey);
        $lists = $query->paginate($pageSize);
        return $this->toJson($lists, $data[$valueField], $primaryKey, $valueField);
    }

    /**
     * 使用方法
     * $query = (new CAutoComplete)->executeByModel($query, ['username'=>Input::get('query')], 'username');
     *
     * @param Query $query 传入Query
     * @param array $data Input数据，或者调用者构造的数组
     * @param string $valueField value字段
     * @param int $pageSize 返回结果的个数
     *
     * @return Query 组装后的query
     * @author 
     **/
    public function executeByModel($model, $data, $valueField, $pageSize=5)
    {
        if(empty($data[$valueField])) {
            throw new ValueFieldNotFoundException();
        }

        $query = $this->buildByModel($model, $data, [$valueField, $model->primaryKey], true);
        $lists = $query->paginate($pageSize);
        return $this->toJson($lists, $data[$valueField], $model->primaryKey, $valueField);
    }

    /**
     * 使用方法
     * $query = (new CAutoComplete)->build($query, $enableOnSearch, ['username'=>Input::get('query')], ['username']);
     *
     * @param Query $query 传入Query
     * @param array $enableOnSearch 搜索配置
     * @param array $data Input数据，或者调用者构造的数组
     * @param mix $selectField 需要select的字段（默认会将primary_key加进去）
     * @param boolean $includePrimaryKey 是否select primary_key，默认为true
     * @param string $primaryKey primary_key字段名称
     *
     * @return Query 组装后的query
     * @author 
     **/
    public function build($query, $enableOnSearch, $data, $selectField, $includePrimaryKey=true, $primaryKey='id')
    {
        if(!is_array($selectField)) {
            $selectField = [$selectField];
        }
        $select = $selectField;
        if($includePrimaryKey == true) {
            $select[] = $primaryKey;
        }
        $select = array_unique($select);

        $query->select($select);
        $query = (new CSearch($query, $enableOnSearch))->query($data);
        return $query;
    }

    /**
     * 使用方法
     * $query = (new CAutoComplete)->buildByModel(new User(), ['username'=>Input::get('query')], ['username']);
     *
     * @param Model $model 传入model
     * @param array $data Input数据，或者调用者构造的数组
     * @param mix $selectField 需要select的字段（默认会将primary_key加进去）
     * @param boolean $includePrimaryKey 是否select primary_key，默认为true
     *
     * @return Query 组装后的query
     * @author 
     **/
    public function buildByModel($model, $data, $selectField, $includePrimaryKey=true)
    {
        $query = $model->newQuery();
        return $this->build($query, $model->enableOnSearch, $data, $selectField, $includePrimaryKey, $model->primaryKey);
    }

    /**
     * 将得到的结果组装成json串
     * @param object $lists 传入lists
     * @param string $keyword 需要查找的keyword
     * @param string $keyField 一般是primary key
     * @param string $valueField 是否select primary_key，默认为true
     */
    private function toJson($lists, $keyword, $keyField, $valueField)
    {
        $result = ["success"=>true, "msg"=>"", "data"=>["query"=>$keyword, "suggestions"=>[]]];
        foreach($lists as $item) {
            $result["data"]["suggestions"][] = ['data'=>$item->$keyField, 'value'=>$item->$valueField];
        }

        return json_encode($result);
    }
}

/**
 * 在传入$data数组中找不到$valueField的对应项
 */
class ValueFieldNotFoundException extends Exception
{

}