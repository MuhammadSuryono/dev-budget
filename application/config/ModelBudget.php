<?php

class ModelBudget
{
    private $mysql;
    private $select;
    private $condition;
    private $from;
    private $joins;

    private $data;
    private $updates;
    private $deletes;
    private $setValueUpdate;

    private $queryString;
    private $inserts;
    private $setValueInsert = [];

    private $orderColumn;
    private $typeOrder = 'asc';
    private $limitData;

    public function load_database($mysql)
    {
        $this->mysql = $mysql;
    }

    public function select($columns = '*')
    {
        $this->reset_value();
        $this->select = 'SELECT ' . $columns;
        return $this;
    }

    public function where($column, $equal, $value = NULL)
    {
        if ($value == NULL) $value = "NULL";
        else $value = " '" . $value . "'";

        $condition = $this->condition;
        $condition = $condition == null ? $column . " " . $equal . " " . $value . " " : ' AND ' . $column . " " . $equal . " " . $value . " ";

        $this->condition .= $condition;
        return $this;
    }

    public function or_where($column, $equal, $value)
    {
        $this->condition .= ' OR ' . $column;
        return $this;
    }

    public function from($table)
    {
        $this->from = $table;
        return $this;
    }

    public function join($table, $on, $type = null)
    {
        $typeJoin = ' JOIN ';
        if ($type != null) $typeJoin = " " . $type . " ". $typeJoin;
        $this->joins .= $typeJoin . $table . ' ON ' . $on;
        return $this;
    }

    public function get($table = null)
    {
        if ($table == null && $this->from == null) die('Database error: Table name not set');
        if ($table != null && $this->from == null) $this->from = $table;

        $this->set_query_select();
        $query = mysqli_query($this->mysql, $this->get_query());
        $dataArray = [];
        while ($row = mysqli_fetch_assoc($query))
        {
            array_push($dataArray, $row);
        }

        $this->data = $dataArray;
        return $this->data;
    }

    public function first()
    {
        try {
            $this->set_query_select();
            $query = mysqli_query($this->mysql, $this->get_query());
            $this->data = mysqli_fetch_assoc($query);
            return $this->data;
        } catch (Exception $exception) {
            die($exception);
        }
    }

    public function get_query()
    {
        return $this->queryString;
    }

    private function set_query_select()
    {
        $query = $this->select . ' FROM ' . $this->from;
        if ($this->joins != null) $query .= $this->joins;
        if ($this->condition != null) $query .= ' WHERE ' . $this->condition;
        if ($this->orderColumn !== null) $query .= ' ORDER BY ' . $this->orderColumn . ' ' . $this->typeOrder;
        if ($this->limitData != 0) $query .= ' LIMIT ' . $this->limitData;

        $this->queryString = $query;
    }

    public function update($table)
    {
        $this->reset_value();
        $this->updates = 'UPDATE '. $table;
        return $this;
    }

    public function set_value_update($column, $value)
    {
        $setValue = $this->setValueUpdate;
        $setValue .= $setValue == null ? " SET `" . $column . "` = " . "'" . $value . "'" : ", `" .$column . "` = " . "'" . $value . "'";

        $this->setValueUpdate = $setValue;
        return $this;
    }

    public function save_update()
    {
        $query = $this->updates . $this->setValueUpdate;
        if ($this->condition != null) $query .= ' WHERE ' . $this->condition;
        else die('Error query: not found condition' );

        $this->queryString = $query;

        return mysqli_query($this->mysql, $query);
    }

    public function insert($table)
    {
        $this->reset_value();
        $this->inserts = 'INSERT INTO ' . $table;
        return $this;
    }

    public function set_value_insert($column, $value)
    {
        $merge = array_merge($this->setValueInsert, [$column => $value]);
        $this->setValueInsert = $merge;
        return $this;
    }

    public function save_insert()
    {
        $query = $this->inserts;
        $columns = '';
        $value = '';

        $index = 0;
        $totalValueInsert = count($this->setValueInsert);

        foreach ($this->setValueInsert as $key => $val) {
            $columns .= $index + 1 != $totalValueInsert ? "`$key`" . "," : "`$key`" ;
            $value .= $index + 1 != $totalValueInsert ? "'".$val."'," : "'".$val."'";

            $index++;
        }
        $query .= ' (' . $columns . ') VALUES (' . $value . ')';
        $this->queryString = $query;

        return mysqli_query($this->mysql, $query);
    }

    public function take($limitData = 0)
    {
        $this->limitData = $limitData;
        return $this;
    }

    public function order_by($column, $typeOrder = 'asc')
    {
        $this->orderColumn = $column;
        $this->typeOrder = $typeOrder;
        return $this;
    }

    private function reset_value()
    {
        $this->select = null;
        $this->condition = null;
        $this->from = null;
        $this->joins = null;

        $this->data = null;
        $this->updates = null;
        $this->setValueUpdate = null;

        $this->queryString = null;
        $this->inserts = null;
        $this->setValueInsert = [];

        $this->orderColumn = null;
        $this->typeOrder = 'asc';
        $this->limitData = 0;
    }

    public function get_id_insert()
    {
        return mysqli_insert_id($this->mysql);
    }

    public function delete($table)
    {
        $this->reset_value();
        $this->deletes = 'DELETE FROM ' . $table;
        return $this;
    }

    public function save_delete()
    {
        $query = $this->deletes;
        if ($this->condition != null) $query .= ' WHERE ' . $this->condition;
        else die('Error query: not found condition' );

        $this->queryString = $query;

        return mysqli_query($this->mysql, $query);
    }
}