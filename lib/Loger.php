<?php

/**
 * *User: zhoubo
 * Date: 16/5/9
 * Time: 下午10:35
 */

class Loger
{
	public $log_app;
	private $_log_data;
	private $_path;

	public function __construct()
	{
		$this->_path = __DIR__ . '/../Runtime/logs/';
	}

	//将两个数据合并到同一个日志
	public function logExt($log_data, $extend, $file_extend = '.log')
	{
		$new_data = array_merge($log_data, $extend);

		return $this->_saveFile($new_data, $file_extend);
	}

	//将数组记录日志
	public function log($log_data, $extend = '.log')
	{
		if (!is_array($log_data))
		{
			$log_data = array('msg' => $log_data);
		}
		return $this->_saveFile($log_data, $extend);
	}

	//记录到错误日志文件中
	public function logErr($log_data)
	{
		return $this->log($log_data, '.error');
	}

	//追加记录到错误日志文件中
	public function logExtErr($log_data, $extend)
	{
		return $this->logExt($log_data, $extend, '.error');
	}

	//追加上一次的log_data数据，需清楚之前积累数据再使用
	public function logAppend($log_data)
	{
		return $this->logExt($this->_log_data, $log_data);
	}

	//用数组的key取对象里的属性记录日志
	public function LogObj($obj, $property)
	{
		$ret = array();
		foreach ($property as $key)
		{
			$ret[$key] = $obj->$key;
		}

		return $this->_saveFile($ret);
	}

	private function _saveFile($log_data, $extend = '.log')
	{
		$this->_log_data = $log_data;

		$file = $this->_path . $this->log_app . $extend;

		$log_content = "date:" . date('Y-m-d H:i:s');
		foreach ($log_data as $k => $v)
		{
			$log_content .= ", $k:$v";
		}
		$log_content .= PHP_EOL;

		return file_put_contents($file, $log_content, FILE_APPEND|LOCK_EX);
	}
}