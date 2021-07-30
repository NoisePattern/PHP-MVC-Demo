<?php

class Table {

	public $columns = [];
	public $model;
	public $rowData;

	/**
	 * Creates HTML to open a table.
	 *
	 * @param array $columns An array of rules describing what each column has to display. There's separate rules for the table head and table body.
	 * @param object $model Model the data is based on. Empty if not based on model.
	 * @param array $row-data An array containing data to be displayed on the table.
	 */
	public function openTable($columns, $model, $rowData){
		$this->columns = $columns;
		$this->model = $model;
		$this->rowData = $rowData;
		return '<table class="table">';
	}

	/**
	 * Creates HTML for table head section.
	 */
	public function tableHead(){
		$row = '<thead><tr>';
		foreach($this->columns as $column){
			// If column is not an array, it refers to a model field.
			if(!is_array($column)){
				$name = $this->model->getLabel($column);
				$row .= '<th scope="col">' . $name . '</th>';
			} else {
				// If array has key 'columnLabel' it is a displayable label.
				if(array_key_exists('columnLabel', $column)){
					$row .= '<th scope="col">' . $column['columnLabel'] . '</th>';
				}
				// If array has key 'field' it refers to a model field.
				else if(array_key_exists('field', $column)){
					$name = $this->model->getLabel($column['field']);
					$row .= '<th scope="col">' . $name . '</th>';
				}
			}
		}
		$row .= '</tr></thead><tbody>';
		return $row;
	}

	/**
	 * Creates HTML for table rows.
	 */
	public function tableRows($start = 0, $number = 0){
		$row = '';
		$size = sizeof($this->rowData);
		$end = $number === 0 ? $size : min($start + $number - 1, $size);
		for($i = $start; $i < $end; $i++){
			$row .= '<tr>';
			foreach($this->columns as $column){
				// If column is not an array, it refers to a model field.
				if(!is_array($column)){
					$row .= '<td class="align-middle">' . $this->rowData[$i][$column] . '</td>';
				} else {
					// If array has key 'field' it refers to a model field.
					if(array_key_exists('field', $column)){
						$data = $this->rowData[$i][$column['field']];
						// If array has key 'format' the data requires formatting.
						if(array_key_exists('format', $column)){
							// If format is 'date'
							if($column['format'][0] === 'date'){
								$data = date($column['format'][1], strtotime($data));
							}
							// If format is 'maxLength'
							else if($column['format'][0] === 'maxLength'){
								if(strlen($data) > $column['format'][1]){
									$data = substr($data, 0, $column['format'][1]);
									$lastPos = strrpos($data, ' ');
									if($lastPos){
										$data = substr($data, 0, $lastPos);
									}
									$data .= '...';
								}
							}
							// If format is 'keyValues'
							else if($column['format'][0] === 'keyValues'){
								$array = $column['format'][1];
								if(array_key_exists($data, $array)){
									$data = $array[$data];
								}
							}
						}
						$row .= '<td class="align-middle">' . $data . '</td>';
					}
					else if(array_key_exists('textLink', $column)){
						$paramName = $column['textLink'][2];
						$paramValue = $this->rowData[$i][$paramName];
						$paramKey = $this->model->getLabel($paramName);
						$row .= '<td><a href="' . URLROOT . '/' . $column['textLink'][1] . '?' . $paramKey . '=' . $paramValue . '">' . $column['textLink'][0] . '</a></td>';
					}
					else if(array_key_exists('buttonLink', $column)){
						$paramName = $column['buttonLink'][2];
						$paramValue = $this->rowData[$i][$paramName];
						$paramKey = $this->model->getLabel($paramName);
						$row .= '<td><a class="btn btn-primary btn-sm" href="' . URLROOT . '/' . $column['buttonLink'][1] . '?' . $paramKey . '=' . $paramValue . '">' . $column['buttonLink'][0] . '</a></td>';
					}
				}
			}
			$row .= '</tr>';
		}
		return $row;
	}

	public function closeTable(){
		return '</tbody></table>';
	}

}
?>