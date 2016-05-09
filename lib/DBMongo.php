<?php
/**
 * *User: zhoubo
 * Date: 16/5/9
 * Time: 下午10:35
 */
class DBMongo
{
	private $connection;
	private $collection;
	public $db;
	private $mongo_connect_string;

	protected $log = null;
	public $log_app = 'mongo';
	protected $error = '';
	public $mdb = NULL;

	public function __construct()
	{
		$this->log = new Loger();
	}

	/**
	 * 连接MongoDB 获取数据库操作句柄
	 *
	 */
	public function connect($connection_string, $dbname, $collection)
	{
		$this->log->log_app = $this->log_app;
		$log_data = array('connect_string' => $connection_string, 'dbname' => $dbname, 'collection' => $collection);

		if( empty($connection_string) || empty($dbname))
		{
			$this->log->logExtErr($log_data, array('msg' => 'connect info empty'));
			return false;
		}


		try
		{
			$options = array('connect' => FALSE);
			$this->connection = new MongoClient($connection_string, $options);
			$connect_ret = $this->connection->connect();

			if ( ! $connect_ret )
			{
				$this->log->logExtErr($log_data, array('msg' => 'try connect fail'));
				return false;
			}

			$this->db = $this->connection->selectDB($dbname);
			if ( ! $this->db )
			{
				$this->log->logExtErr($log_data, array('msg' => 'select db fail'));
				return false;
			}

			$this->mdb = $this->db->{$collection};

			return true;
		}
		catch (Exception $e)
		{
			$mdb_error = $e->getMessage();
			$this->error = $mdb_error;
			$this->log->logExtErr($log_data, array('mdb_error' => $mdb_error, 'msg' => 'connect fail'));

			return false;
		}
	}

	/**
	 * 设置表
	 *
	 */
	public function setCollection($collection)
	{
		if( empty($collection))
		{
			$this->log->logErr("In order to retreive documents from MongoDB, a collection name must be passed.");
			return false;
		}

		$this->mdb = $this->db->{$collection};
	}

	/**
	 *  update更新一条记录
	 *
	 */
	public function update($where, $set_data = array())
	{
		try
		{
			return $this->mdb->update($where, $set_data, array('safe' => true, 'multiple' => false, 'upsert' => false));
		}
		catch(Exception $e)
		{
			$mdb_error = $e->getMessage();
			$this->error = $mdb_error;
			$log_data = array('where' => json_encode($where), 'set_data' => json_encode($set_data), 'mdb_error' => $mdb_error, 'msg' => 'update error');
			$this->log->logErr($log_data);
			return false;
		}
	}

	/**
	 * 不存在则创建
	 * @param unknown_type $where
	 */
	public function updateUpsert($where, $set_data)
	{
		try
		{
			return $this->mdb->update($where, $set_data, array('safe' => true, 'multiple' => false, 'upsert' => true));
		}
		catch(Exception $e)
		{
			$mdb_error = $e->getMessage();
			$this->error = $mdb_error;
			$log_data = array('where' => json_encode($where), 'set_data' => json_encode($set_data), 'mdb_error' => $mdb_error, 'msg' => 'update error');
			$this->log->logErr($log_data);
			return false;
		}
	}

	/**
	 * 批量写入
	 * @param unknown_type $set_data
	 */
	public function batchInsert($set_data = array())
	{
		try
		{
			$this->mdb->batchInsert($set_data, array('safe' => true));
			return $set_data;
		}
		catch(Exception $e)
		{
			$mdb_error = $e->getMessage();
			$this->error = $mdb_error;
			$log_data = array('set_data' => json_encode($set_data), 'mdb_error' => $mdb_error, 'msg' => 'batch insert error');
			$this->log->logErr($log_data);
			return false;
		}
	}

	/**
	 * 单条记录写入
	 * @param unknown_type $set_data
	 */
	public function insert($set_data = array())
	{
		try
		{
			return $this->mdb->insert($set_data, array('safe' => true));
		}
		catch(Exception $e)
		{
			$mdb_error = $e->getMessage();
			$this->error = $mdb_error;
			$log_data = array('set_data' => json_encode($set_data), 'mdb_error' => $mdb_error, 'msg' => 'insert error');
			$this->log->logErr($log_data);
			return false;
		}
	}

	/**
	 * grid记录写入
	 */
	public function store_bytes($set_data, $parame = array())
	{
		try
		{
			$grid = $this->db->getGridFS();
			return $grid->storeBytes($set_data, $parame);
		}
		catch(Exception $e)
		{
			$mdb_error = $e->getMessage();
			$this->error = $mdb_error;
			$log_data = array('set_data' => $set_data, 'mdb_error' => $mdb_error, 'msg' => 'store bytes error');
			$this->log->logErr($log_data);
			return false;
		}
	}

	/**
	 * grid记录读取
	 */
	public function grid_find_one_bytes($where = array())
	{
		try
		{
			$grid = $this->db->getGridFS();
			$data = $grid->findOne($where);

			if(empty($data))
			{
				$mongo_host = defined('MONGO_HOST') ? MONGO_HOST : null;
				$log_data = array('where' => json_encode($where), 'result' => json_encode($where), 'msg' => '$data is error ['.$mongo_host.']');
				$this->log->logErr($log_data);
				return FALSE;
			}
			$result = $data ->getBytes();
			if(empty($result))
			{
				$log_data = array('where' => json_encode($where), 'result' => json_encode($result), 'msg' => 'get store error');
				$this->log->logErr($log_data);
			}
			return $result;
		}
		catch(Exception $e)
		{
			$mdb_error = $e->getMessage();
			$this->error = $mdb_error;
			$log_data = array('where' => json_encode($where), 'mdb_error' => $mdb_error, 'msg' => 'getn store bytes error');
			$this->log->logErr($log_data);
			return false;
		}
	}

	public function find($where = array(), $select = array())
	{
		try
		{
			$results = $this->mdb->find($where, $select);
			$returns = array();
			if (empty($results))
			{
				return $returns;
			}

			foreach($results as $result)
			{
				$returns[] = $result;
			}

			return $returns;
		}
		catch(Exception $e) //MongoCursorException
		{
			$mdb_error = $e->getMessage();
			$this->error = $mdb_error;
			$log_data = array('where' => json_encode($where), 'select' => json_encode($select), 'mdb_error' => $mdb_error, 'msg' => 'find error');
			$this->log->logErr($log_data);

			return false;
		}
	 }
	public function getFileFromMongo($value='')
	{
		try
		{
			$grid = $this->db->getGridFS();
			$data = $grid->findOne($value);
			// print_r($data);exit;
			$file_name = isset($data->file['filename']) ? $data->file['filename'] : '';
			if(empty($file_name))
			{
				$log_data = array('value' => json_encode($value), 'result' => $file_name, 'msg' => 'get store error');
				$this->log->logErr($log_data);
			}

			return $data->getBytes();
		}
		catch(Exception $e)
		{
			$mdb_error = $e->getMessage();
			$this->error = $mdb_error;
			$log_data = array('where' => json_encode($where), 'mdb_error' => $mdb_error, 'msg' => 'getn store bytes error');
			$this->log->logErr($log_data);
			return false;
		}
	}
	public function findOne($where = array(), $select = array())
	{
		try
		{
			$results = $this->mdb->findOne($where, $select);
			$returns = array();
			if (empty($results))
			{
				return $returns;
			}

			foreach($results as $key => $result)
			{
				$returns[$key] = $result;
			}

			return $returns;
		}
		catch(Exception $e) //MongoCursorException
		{
			$mdb_error = $e->getMessage();
			$this->error = $mdb_error;
			$log_data = array('where' => json_encode($where), 'select' => json_encode($select), 'mdb_error' => $mdb_error, 'msg' => 'find error');
			$this->log->logErr($log_data);

			return false;
		}
	 }

	public function findOneBySort($where = array(), $select = array(), $sort = array())
	{
		try
		{
			$results = $this->mdb->find($where, $select)->sort($sort)->limit(1);
			$returns = array();
			if (empty($results))
			{
				return $returns;
			}

			foreach($results as $key => $result)
			{
				$returns = $result;
			}

			return $returns;
		}
		catch(Exception $e) //MongoCursorException
		{
			$mdb_error = $e->getMessage();
			$this->error = $mdb_error;
			$log_data = array('where' => json_encode($where), 'select' => json_encode($select), 'mdb_error' => $mdb_error, 'msg' => 'find error');
			$this->log->logErr($log_data);

			return false;
		}
	 }

	public function group($key, $initial, $reduce, $collection = array())
	{
    	try
    	{
    		$collection = array('condition' => $collection);
    		$result = $this->mdb->group($key, $initial, $reduce, $collection);
    		return $result;
    	}
    	catch(Exception $e)
		{
			$mdb_error = $e->getMessage();
			$this->error = $mdb_error;
			$log_data = array('key' => json_encode($key), 'initial' => json_encode($initial), 'reduce' => json_encode($reduce), 'collection' => json_encode($collection), 'mdb_error' => $mdb_error, 'msg' => 'group error');
			$this->log->logErr($log_data);

			return false;
		}
	}

	//返回最后一次的错误
	public function getLastError()
	{
		return $this->error;
	}

	/**
	 * 安全删除，同时获取影响行数，只删除一条
	 * @param unknown_type $table_name
	 * @param unknown_type $where
	 */
	public function removeSafe($where)
	{
		try
		{
			$ret = $this->mdb->remove($where, array('justOne' => true, 'safe' => true));
			//affected row
			return $ret['n'];
		}
		catch (Exception $e)
		{
			$mdb_error = $e->getMessage();
			$this->error = $mdb_error;
			$log_data = array('where' => json_encode($where), 'mdb_error' => $mdb_error, 'msg' => 'remove safe error');
			$this->log->logErr($log_data);

			return false;
		}
	}

	public function dropDatabase()
	{
		try
		{
			$ret = $this->db->drop();

			//affected row
			$status = isset($ret['ok']) ? $ret['ok'] : 0;
			return $status;
		}
		catch (Exception $e)
		{
			$mdb_error = $e->getMessage();
			$this->error = $mdb_error;
			$log_data = array('mdb_error' => $mdb_error, 'msg' => 'dropDatabase error');
			$this->log->logErr($log_data);

			return false;
		}
	}

	/* add_index
	 *
	 * @usage : $this->mongo_db->add_index('foo', array('first_name' => 'ASC', 'last_name' => -1), array('unique' => true)));
	 */
	public function addIndex($keys = array(), $options = array())
	{
		if (empty($keys)) {
			$this->log->logErr("Index could not be created to MongoDB Collection because no keys were specified");
			return false;
		}
		if(!is_array($keys)){
			$keys = array($keys => 1);
		}

		foreach($keys as $col => $val)
		{
			if ($val == -1 || $val == false || strtolower($val) == 'desc')
			{
				$keys[$col] = -1;
			}
			else
			{
				$keys[$col] = 1;
			}
		}

		//在此没有对$options数组的有效性进行验证

		if (true == $this->mdb->ensureIndex($keys, $options))
		{
			return true;
		}
		else
		{
			$this->log->logErr("An error occured when trying to add an index to MongoDB Collection");
			return false;
		}
	}

	public function grid_find_one_name($where = array())
	{
		try
		{
			$grid = $this->db->getGridFS();
			$data = $grid->findone($where);
			$file_name = isset($data->file['filename']) ? $data->file['filename'] : '';
			if(empty($file_name))
			{
				$log_data = array('where' => json_encode($where), 'result' => $file_name, 'msg' => 'get store error');
				$this->log->logErr($log_data);
			}

			return $file_name;
		}
		catch(Exception $e)
		{
			$mdb_error = $e->getMessage();
			$this->error = $mdb_error;
			$log_data = array('where' => json_encode($where), 'mdb_error' => $mdb_error, 'msg' => 'getn store bytes error');
			$this->log->logErr($log_data);
			return false;
		}
	}


	/**
	 * 关闭数据库连接
	 *
	 */
	public function close()
	{
		$this->connection->close();
	}

	public function __destruct()
	{
		return self::close();
	}
}