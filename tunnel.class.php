<?php

class MysqlTunnelClient
{
    public $url;
    public $errors = array();
    public $num_rows = 0;
    private $dbData;
    private $result;

    public function __construct($cfg)
    {
        $this->url = $cfg['tunnel_url'];
        $this->dbData = $cfg['db_data'];
    }

    private function sendQuery($query, $array = false)
    {
        $query = base64_encode($query);

        $data = json_encode(array('db' => $this->dbData, 'query' => $query));

        $link = $this->makeRequest($this->url, $data);

        $this->result = json_decode($link, true);
        $this->num_rows = isset($this->result['num_rows']) ? $this->result['num_rows'] : 0;

        if (isset($this->result['errors'])) {
            $this->errors();
        }

        return $this->result['result'];
    }

    public function query($query)
    {
        $this->sendQuery($query, true);
        return $this;
    }

    public function get($table_name, $limit = null, $offset = null)
    {
        $sql = "SELECT * FROM {$table_name}";

        if (isset($limit) || isset($offset) && isset($limit)) {
            if ($offset == 0) {
                $offset = '';
            } else {
                $offset .= ', ';
            }

            $sql .= ' LIMIT '.$offset.$limit;
        }

        return $this->sendQuery($sql, true);
    }

    public function num_rows()
    {
        return $this->num_rows;
    }

    public function row($index = 0)
    {
        if (!isset($this->result['result'][$index])) {
            return false;
        }

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

    public function update($table, $values, $where = array(), $limit = false)
    {
        foreach ($values as $key => $val) {
            $valstr[] = $key . ' = ' . $val;
        }

        foreach ($where as $key => $val) {
            $wherestr[] = $key . ' = "' . $val . '"';
        }

        $limit = (!$limit) ? '' : ' LIMIT '.$limit;

        $sql = 'UPDATE '.$table.' SET '.implode(', ', $valstr);
        $sql .= ($where != '' and count($where) >=1) ? ' WHERE '.implode(', ', $wherestr) : '';
        $sql .= $orderby.$limit;

        return $sql;
    }

    private function errors()
    {
        foreach ($this->result['errors'] as $error) {
            echo $error."\n<br />";
        }
        die;
    }

    private function makeRequest($url, $data)
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_VERBOSE, true);

        return curl_exec($ch);
    }
}

/* end of file */
