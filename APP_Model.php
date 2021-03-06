<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class APP_Model extends CI_Model {

	public $table;

    public function __construct(){

		parent::__construct();

    }

	function get($filter=array(), $select=null) {

		if ($filter === null) {
			return null;
		}

		if (is_array($filter)) {
			foreach($filter as $field=>$value) {
				if ($field == 'order_by') $this->db->order_by($value);
				else {
					($value === false) ? $this->db->where($field) : $this->db->where($field, $value);
				}
			}
		}
		else {
			$this->db->where('id', $filter);
		}

		if ($select) {
			$this->db->select($select, false);
		}
		$this->db->limit(1);
		$query = $this->db->get($this->table);

		$row = $query->row_array();

		if ($select && sizeof(explode(",", $select)) == 1) {
			if (stripos($select, ' as ') !== false) {
				$select = explode(' as ', $select);
				return $row[$select[1]];
			}
			else return $row[$select];
		}

		return $row;
	}

	public function listing($filter=array(), $key_value=null) {

		if (isset($filter['order_by'])) {
			if (is_array($filter['order_by'])) {
				foreach ($filter['order_by'] as $order_by) {
					$this->db->order_by($order_by);
				}
			}
			else $this->db->order_by($filter['order_by']);
			unset($filter['order_by']);
		}

		if (!empty($filter['group_by'])) {
			$this->db->group_by($filter['group_by']);
			unset($filter['group_by']);
		}

		if (!empty($filter['limit'])) {
			$this->db->limit($filter['limit']);
			$this->db->offset($filter['offset']);
			unset($filter['limit'], $filter['offset']);
		}

		if (is_array($filter)) {
			foreach ($filter as $field=>$v) {
				if ($v === false) {
					$this->db->where($field);
				}
				else {
					$this->db->where($field, $v);
				}
			}
		}

		if ($key_value) {
			$result = array();

			$key_value = explode("=>", $key_value);
			$key = trim($key_value[0]);
			$value = trim($key_value[1]);
		}

		if (!empty($value)) $this->db->select("$key, $value");

		$query = $this->db->get($this->table);

		if ($key_value) {
			$result = array();

			foreach ($query->result_array() as $row) {
				if (!$value) $result[$row["$key"]] = $row;
				else $result[$row["$key"]] = $row["$value"];
			}
			return $result;
		}

		return $query->result_array();
	}

	public function save($data, $key="id", $id=NULL) {
		unset($data['submit']);

		if ($id) {
			$this->db->where($key, $id);
			$query = $this->db->get($this->table);

			$row = $query->row_array();
		}
		elseif ($data["$key"]) {
			$this->db->where($key, $data["$key"]);
			$query = $this->db->get($this->table);

			$row = $query->row_array();
		}

		foreach ($data as $k=>$v) {
			if ($v === null) {
				$this->db->set($k, 'DEFAULT', FALSE);
				unset($data[$k]);
			}
		}

		if (($data["$key"] && $data["$key"] == $row["$key"]) || $id) {

			$data['updated_by'] = $_SESSION['user_id'];
			$data['updated_at'] = date('Y-m-d H:i:s');
			if ($id) $this->db->where($key, $id);
			else $this->db->where($key, $data["$key"]);
			$response = $this->db->update($this->table, $data);

			$id = $data["$key"];
		}
		else {
			$data['created_by'] = $_SESSION['user_id'];
			$data['created_at'] = date('Y-m-d H:i:s');
			$response = $this->db->insert($this->table, $data);

			$id = $this->db->insert_id();
		}

		if (!$response) {
			throw new Exception('An error has occurred while trying to save the data.');
		}

		return $id;
	}

	public function count($filter=array()) {

		unset($filter['limit'], $filter['offset'], $filter['order_by'], $filter['group_by']);

		if (is_array($filter)) {
			foreach ($filter as $field=>$value) {
				if ($value === false) {
					$this->db->where($field);
				}
				else {
					$this->db->where($field, $value);
				}
			}
		}

		$count = $this->db->count_all_results($this->table);

		return $count;
	}

	public function add_batch($data) {
		foreach ($data as $k=>$row) {
			$data[$k]['created_by'] = $_SESSION['user_id'];
			$data[$k]['created_at'] = date('Y-m-d H:i:s');
		}
		$this->db->insert_batch($this->table, $data);
		//echo $this->db->last_query();
	}

	public function delete($filter=null) {

		if ($filter === null) {
			return null;
		}
		if (is_array($filter)) {
			foreach($filter as $field=>$value) {
				($value === false) ? $this->db->where($field) : $this->db->where($field, $value);
			}
		}
		else {
			$this->db->where('id', $filter);
		}


		$response = $this->db->delete($this->table);

		if (!$response) {
			throw new Exception('An error has occurred while trying to delete the data.');
		}
	}
}

?>
