<?php

class MysqlTunnelClient
{
	public $url, $errors = array(), $num_rows = 0;
	private $dbData, $result;

	function __construct($cfg)
	{
		$this->url		= $cfg['tunnel_url'];
		$this->dbData	= $cfg['db_data'];
	}

	private function sendQuery($query, $array = FALSE)
	{
		$query = base64_encode($query);

		$data = json_encode(array('db' => $this->dbData, 'query' => $query));

		$link = $this->makeRequest($this->url, $data);

		$this->result = json_decode($link->body, TRUE);
		$this->num_rows = isset($this->result['num_rows']) ? $this->result['num_rows'] : 0;

		if(isset($this->result['errors']))
		{
			$this->errors();
		}

		return $this->result['result'];

	}

	public function query($query)
	{
		$this->sendQuery($query, TRUE);
		return $this;
	}

	public function get($table_name, $limit = NULL, $offset = NULL)
	{
		$sql = "SELECT * FROM {$table_name}";

		if(isset($limit) || isset($offset) && isset($limit))
		{
			if ($offset == 0)
			{
				$offset = '';

			}else{
				$offset .= ', ';

			}

			$sql .= ' LIMIT '.$offset.$limit;
		}

		return $this->sendQuery($sql, TRUE);

	}

	public function num_rows()
	{
		return $this->num_rows;
	}

	public function row( $index = 0 )
	{
		if(!isset($this->result['result'][$index])) return FALSE;

		return ($this->num_rows > 1) ? $this->result['result'][$index]: $this->result['result'];
	}

	public function fetch_array()
	{
		return $this->result['result'];
	}

	public function fetch_object()
	{
		return (object) $this->result['result'];
	}

	public function update($table, $values, $where = array(), $limit = FALSE)
	{
		foreach ($values as $key => $val)
		{
			$valstr[] = $key . ' = ' . $val;
		}

		foreach ($where as $key => $val)
		{
			$wherestr[] = $key . ' = "' . $val . '"';
		}

		$limit = (!$limit) ? '' : ' LIMIT '.$limit;

		$sql = 'UPDATE '.$table.' SET '.implode(', ', $valstr);
		$sql .= ($where != '' AND count($where) >=1) ? ' WHERE '.implode(', ', $wherestr) : '';
		$sql .= $orderby.$limit;

		return $sql;
	}

    private function errors()
	{
		foreach($this->result['errors'] as $error)
		{
			echo $error."\n<br />";
		}
		die;
	}

    private function makeRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                                                                                             
        return curl_exec($ch);

    }

}

/* end of file */
