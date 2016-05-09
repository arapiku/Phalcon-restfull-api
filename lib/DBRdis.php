<?php

class DBRedis
{
	private $connection;
	private $collection;
	public $db;
	private $redis_connect_string;

	protected $log = null;
	public $log_app = 'redis';
	protected $error = '';
	public $expire_time = 86400;

	public function __construct()
	{
		$this->log = new Loger();
	}

	/*
	 * 连接redis
	 *
	 */
	public function connect($host, $port)
	{
		$this->log->log_app = $this->log_app . '_' . date('Ymd');
		$log_data = array('host' => $host, 'port' => $port);

		if( empty($host) || empty($port))
		{
			$this->log->logExtErr($log_data, array('msg' => 'connect info empty'));
			return false;
		}

		try
		{
			$this->connection = new Redis();
			if ( ! $this->connection->connect($host, $port) )
			{
				$this->log->logExtErr($log_data, array('msg' => 'try connect fail'));
				return false;
			}

			return true;
		}
		catch (Exception $e)
		{
			$connect_error = $e->getMessage();
			$this->error = $connect_error;
			$this->log->logExtErr($log_data, array('connect_error' => $connect_error, 'msg' => 'connect fail'));

			return false;
		}
	}

	/*
	**向队列右侧插入数据
	*/
	public function rPush($key, $value)
	{
		$log_data = array();
		$log_data['key'] = $key;
		$log_data['value'] = $value;
		try
		{
			return $this->connection->rPush($key, $value);
		}catch(Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'rpush error'));
			return false;
		}
	}

	public function hSet($key, $score, $value)
	{
		$log_data = array();
		$log_data['key'] = $key;
		$log_data['score'] = $score;
		$log_data['value'] = $value;
		try
		{
			return $this->connection->hset($key, $score, $value);
		}catch(Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'hset error'));
			return false;
		}
	}

	public function hGet($key, $score)
	{
		$log_data = array();
		$log_data['key'] = $key;
		$log_data['score'] = $score;
		try
		{
			return $this->connection->hget($key, $score);
		}catch(Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'hget error'));
			return false;
		}
	}

	public function hDel($key, $score)
	{
		$log_data = array();
		$log_data['key'] = $key;
		$log_data['score'] = $score;
		try
		{
			return $this->connection->hdel($key, $score);
		}catch(Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'hdel error'));
			return false;
		}
	}

	public function hVals($key)
	{
		$log_data = array();
		$log_data['key'] = $key;
		try
		{
			return $this->connection->hvals($key);
		}catch(Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'hvals error'));
			return false;
		}
	}

	public function zincr($sets, $key)
	{
		$log_data = array();
		$log_data['sets'] = $sets;
		$log_data['key'] = $key;
		try
		{
			return $this->connection->zincrby($sets, 1, $key);
		}catch(Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'zincr error'));
			return false;
		}
	}

	public function zrangeAll($sets)
	{
		$log_data = array();
		$log_data['sets'] = $sets;
		try
		{
			return $this->connection->zrange($sets, 0, -1, "withscores");
		}catch(Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'zrangeAll error'));
			return false;
		}
	}

	/*
	**set
	*/
	public function set($key, $value, $expire = 86400)
	{
		$this->expire_time = $expire;
		$log_data = array();
		$log_data['key'] = $key;
		$log_data['value'] = $value;
		try
		{
			if($expire === false)
			{
				return $this->connection->set($key, $value);
			}
			else
			{
				return $this->connection->set($key, $value, $this->expire_time);
			}

		}catch(Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'set error'));
			return false;
		}
	}

	/*
	**get
	*/
	public function get($key)
	{
		$log_data = array();
		$log_data['key'] = $key;
		try
		{
			return $this->connection->get($key);
		}catch(Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'get error'));
			return false;
		}
	}

	/*
	**按照范围删掉其他数据
	*/
	public function lTrim($key, $start, $end)
	{
		$log_data = array();
		$log_data['key'] = $key;
		$log_data['start'] = $start;
		$log_data['end'] = $end;
		try
		{
			return $this->connection->lTrim($key, $start, $end);
		}catch(Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'ltrim error'));
			return false;
		}
	}

	/*
	**左侧第一个数出列
	*/
	public function lPop($key)
	{
		$log_data = array();
		$log_data['key'] = $key;
		try
		{
			return $this->connection->lPop($key);
		}catch(Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'lpop error'));
			return false;
		}
	}

	/*
	**计算队列个数
	*/
	public function lSize($key)
	{
		$log_data = array();
		$log_data['key'] = $key;
		try
		{
			return $this->connection->lSize($key);
		}catch(Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'lsize error'));
			return false;
		}
	}

	/*
	**按照起止数获取数据
	*/
	public function lGetRange($key, $start, $end)
	{
		$log_data = array();
		$log_data['key'] = $key;
		$log_data['start'] = $start;
		$log_data['end'] = $end;
		try
		{
			return $this->connection->lRange($key, $start, $end);
		}catch(Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'lgetrange error'));
			return false;
		}
	}

	/**
	 * 添加到有序集合 覆盖原有的值
	 * */
	public function zAdd($key, $score, $value)
	{
		$log_data = array();
		$log_data['key'] = $key;
		$log_data['score'] = $score;
		$log_data['value'] = $value;
		try
		{
			return $this->connection->zAdd ( $key, $score, $value );
		}catch (Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'zAdd error'));
			return false;
		}
	}

	public function sAdd($key, $value)
	{
		$log_data = array();
		$log_data['key'] = $key;
		$log_data['value'] = $value;
		try
		{
			return $this->connection->sAdd($key, $value);
		}catch (Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'sAdd error'));
			return false;
		}
	}


	/**
	 * 删除名称为key 的zset 中的元素member (集合)
	 * */
	public function zRem($key, $member)
	{
		$log_data = array();
		$log_data['key'] = $key;
		$log_data['member'] = $member;
		try
		{
			return $this->connection->zRem ( $key, $member );
		}catch (Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'zRem error'));
			return false;
		}
	}

	public function sRem($key, $member)
	{
		$log_data = array();
		$log_data['key'] = $key;
		$log_data['member'] = $member;
		try
		{
			return $this->connection->sRem ( $key, $member );
		}catch (Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'sRem error'));
			return false;
		}
	}

	public function setTimeout($key, $expire = 86400)
	{
		$log_data = array();
		$log_data['key'] = $key;
		$log_data['expire'] = $expire;
		try
		{
			return $this->connection->setTimeout($key, $expire);
		}catch (Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'zExpire error'));
			return false;
		}
	}

	public function hgetall($key)
	{
		$log_data = array();
		$log_data['key'] = $key;
		try
		{
			return $this->connection->hgetall($key);
		}catch (Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'hgetall error'));
			return false;
		}
	}

	public function hincrby($key, $set, $val)
	{
		$log_data = array();
		$log_data['key'] = $key;
		$log_data['set'] = $set;
		$log_data['val'] = $val;
		try
		{
			return $this->connection->hincrby($key, $set, $val);
		}catch (Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'hincrby error'));
			return false;
		}
	}

	public function setExpire($key, $expire = 86400)
	{
		$log_data = array();
		$log_data['key'] = $key;
		$log_data['expire'] = $expire;
		try
		{
			return $this->connection->expire($key, $expire);
		}catch (Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'expire error'));
			return false;
		}
	}

	public function exists($key)
	{
		$log_data = array();
		$log_data['key'] = $key;
		try
		{
			return $this->connection->exists($key);
		}catch (Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'exists error'));
			return false;
		}
	}


	/**
	 * 追加原有的值 (集合)
	 * */
	public function zIncrBy($key, $value, $member)
	{
		$log_data = array();
		$log_data['key'] = $key;
		$log_data['value'] = $value;
		$log_data['member'] = $member;
		try
		{
			return $this->connection->zIncrBy ( $key, $value, $member );
		}catch (Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'zIncrBy error'));
			return false;
		}
	}

	public function incrby($key, $value)
	{
		$log_data = array();
		$log_data['key'] = $key;
		$log_data['value'] = $value;
		try
		{
			return $this->connection->incrby ( $key, $value);
		}catch (Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'IncrBy error'));
			return false;
		}
	}

	public function zremrangebyscore($key, $value, $member)
	{
		$log_data = array();
		$log_data['key'] = $key;
		$log_data['value'] = $value;
		$log_data['member'] = $member;
		try
		{
			return $this->connection->zremrangebyscore ( $key, $value, $member );
		}catch (Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'zremrangebyscore error'));
			return false;
		}
	}

	/**
	 * 返回名称为key 的zset 中member 元素的排名 (按score 从小到大排序)即下标 (集合)
	 * */
	public function zRank($key, $member)
	{
		$log_data = array();
		$log_data['key'] = $key;
		$log_data['member'] = $member;
		try
		{
			return $this->connection->getRedis ( $key, 'slave' )->zRank ( $key, $member );
		}catch (Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'zRank error'));
			return false;
		}
	}

	/**
	 * 返回给定元素对应的score (集合)
	 * */
	public function zScore($key, $member)
	{
		$log_data = array();
		$log_data['key'] = $key;
		$log_data['member'] = $member;
		try
		{
			return $this->connection->zScore ( $key, $member );//getRedis ( $key, 'slave' )->
		}catch (Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'zScore error'));
			return false;
		}
	}

	/**
	 * 返回名称为key 的zset（按score 从大到小排序）中的index 从start 到end 的所有元素 (集合)
	 * */
	public function zRevRange($key, $start, $end, $withscore = false)
	{
		$log_data = array();
		$log_data['key'] = $key;
		$log_data['start'] = $start;
		$log_data['end'] = $end;
		$log_data['withscore'] = $withscore;
		try
		{
			return $this->connection->zRevRange ( $key, $start, $end, $withscore );
		}catch (Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'zRevRange error'));
			return false;
		}
	}

	/**
	 * 返回集合中元素个数
	 * */
	public function zCard($key)
	{
		$log_data = array();
		$log_data['key'] = $key;
		try
		{
			return $this->connection->zCard ( $key );
		}catch (Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'zCard error'));
			return false;
		}
	}

	/**
	 * 删除
	 * */
	public function del($key)
	{
		$log_data = array();
		$log_data['key'] = $key;
		try
		{
			return $this->connection->delete ( $key );
		}catch (Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'delete error'));
			return false;
		}
	}

	public function expire($key, $expire = 3600)
	{
		$log_data = array();
		$log_data['key'] = $key;
		$log_data['expire'] = $expire;
		try
		{
			return $this->connection->expire($key, $expire);
		}catch (Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'expire error'));
			return false;
		}
	}

	/**
	 * 返回集合中score 在给定区间的元素
	 * */
	public function zRange($key, $start, $end, $withscore = false)
	{
		$log_data = array();
		$log_data['key'] = $key;
		$log_data['start'] = $start;
		$log_data['end'] = $end;
		$log_data['widthscore'] = $withscore;
		try
		{
			return $this->connection->zRange ( $key, $start, $end, $withscore );
		}catch (Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'zRange error'));
			return false;
		}
	}

	public function setbit($key, $data, $value)
	{
		$log_data = array();
		$log_data['key'] = $key;
		$log_data['data'] = $data;
		$log_data['value'] = $value;
		try
		{
			return $this->connection->setbit($key, $data, $value);
		}catch(Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'setbit error'));
			return false;
		}
	}

	public function getbit($key, $value)
	{
		$log_data = array();
		$log_data['key'] = $key;
		try
		{
			return $this->connection->getbit($key, $value);
		}catch(Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'getbit error'));
			return false;
		}
	}

	public function bitcount($key)
	{
		$log_data = array();
		$log_data['key'] = $key;
		try
		{
			return $this->connection->bitcount($key);
		}catch(Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'bitcount error'));
			return false;
		}
	}
	public function sort($key,$sort)
	{
		$log_data = array();
		$log_data['key'] = $key;
		try
		{
			return $this->connection->sort($key,$sort);
		}catch(Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'bitcount error'));
			return false;
		}
	}
	public function zRangeByScore($key,$start,$end,$false=false)
	{
		$log_data = array();
		$log_data['key'] = $key;
		try
		{
			return $this->connection->zRangeByScore($key,$start,$end);
		}catch(Exception $e)
		{
			$error = $e->getMessage();
			$this->error = $error;
			$this->log->logExtErr($log_data, array('error' => $error, 'msg' => 'bitcount error'));
			return false;
		}
	}
	public function close()
	{
		$this->connection->close();
	}
}