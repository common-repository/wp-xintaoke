<?php
/**
 * API:open.tuan.category.get
 * 
 * @author lsj
 *
 */
class TuanCategoryGetRequest
{
	/**
	 * 查询字段：TuanCategory数据结构的公开信息字段列表，多个数值以半角逗号(,)分隔
	 */
	private $fields;

	
	private $apiParams = array();
	
	public function setFields($fields)
	{
		$this->fields = $fields;
		$this->apiParams["fields"] = $fields;
	}
	public function getFields()
	{
		return $this->fields;
	}

	public function getApiMethodName()
	{
		return "open.tuan.category.get";
	}
	public function getApiParams()
	{
		
		return $this->apiParams;
	}
	public function putOtherTextParam($key,$value)
	{
		$this->apiParams[$key] = $value;
		$this->$key = $value;
	}
	
}
?>