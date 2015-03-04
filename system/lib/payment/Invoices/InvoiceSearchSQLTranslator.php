<?php

class SJB_InvoiceSearchSQLTranslator extends SJB_SearchSqlTranslator
{
	
	public function _getSelectStatement()
	{
		return 'SELECT `' . $this->object_table_prefix . '`.`sid` as `object_sid` ';
	}

	public function _getFromStatement($inner_join = '')
	{
		$sql = '';
		$inner = '';
		if (!empty($inner_join)){
			foreach ($inner_join as $key => $val) {
				if (isset($val['sort_field']) && is_array($val['sort_field'])) {
					$i = 0; $brackets = "";
					foreach ($val['sort_field'] as $user_group => $sort_field) {
						if (count($val['sort_field']) > $i) {
							$sql .= ", IF(";
							$brackets .= ")";
						}
						if (is_array($sort_field)) {
							$name = "CONCAT_WS(' '";
							foreach ($sort_field as $column) {
								$name .= ", `{$key}`." . $column;
							}
							$name .= ")";
						} else {
							$name = "`{$key}`." . $sort_field;
						}
						$sql .= "`{$key}`.`user_group_sid`= {$user_group} , {$name}";
						$i++;
					}
					$sql .= ", `{$key}`.username" . $brackets . " as `username`";
				}
				$inner .= $val['join'] . " `{$key}`  ON `{$key}`.{$val['join_field']} = `{$this->object_table_prefix}`.{$val['join_field2']} ";
			}
		}
		$from_block =  "FROM `{$this->object_table_prefix}` ";
		return $sql . $from_block .'  '. $inner;
	}
}
