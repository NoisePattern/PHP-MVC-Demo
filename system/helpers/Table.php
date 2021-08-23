<?php

class Table extends HtmlHelper {
	public $model;
	public $columns = [];
	public $rowData;
	public $useDefaults = true;
	public $elementDefaultClass = [				// Class attribute values required by by various HTML elements, as mandated by Bootstrap v5.
		'table' => 'table',						// Class required by table element.
		'buttonLink' => 'btn btn-primary'		// Class required by button element.
	];

	/**
	 * Sets up table data.
	 *
	 * @param array $columns An array of rules describing what each column has to display. See rules detailed at top.
	 * @param object $model Model the data is based on. Empty if not based on model.
	 * @param array $row-data An array containing data to be displayed on the table.
	 */
	public function __construct( $model, $columns, $rowData, $useDefaults = true){
		$this->model = $model;
		$this->columns = $columns;
		$this->rowData = $rowData;
		$this->useDefaults = $useDefaults;
	}

	/**
	 * Creates HTML to open a table.
	 *
	 * @param array $tableOptions An array of attributes as key-value pairs to format the table element.
	 * @param array $headRowOptions An array containing attributes as key-value pairs for thead's row element.
	 * @param array $headCellOptions An array containing attributes as key-value pairs for thead's th elements.
	 * @param array $bodyRowOptions An array containing attributes as key-value pairs for tbody's row elements.
	 * @param array $bodyCellOptions An array containing attributes as key-value pairs for tbody's td elements.
	 * @return string Returns a HTML table.
	 */
	public function createTable($tableOptions = [], $headRowOptions = [], $headCellOptions = [], $bodyRowOptions = [], $bodyCellOptions = []){
		Html::setAttribute($tableOptions, ['class' => $this->getDefaultClass('table')]);
		// Loop through rules of each column and construct table head.
		$head = '';
		foreach($this->columns as $column){
			// If rule is not an array, it is a string specifying a model value to show.
			if(!is_array($column)){
				$name = $this->model->getLabel($column);
				$cell = Html::th($name, $headCellOptions);
			} else {
				$thisOptions = $headCellOptions;
				// Key 'class' indicates custom class for this column only.
				if(array_key_exists('theadClass', $column)){
					$thisOptions['class'] = empty($thisOptions['class']) ? $column['theadClass'] : $thisOptions['class'] . ' ' . $column['theadClass'];
				}
				// Key 'columnLabel' indicates text to be shown.
				if(array_key_exists('columnLabel', $column)){
					$cell = Html::th($column['columnLabel'], $thisOptions);
				}
				// Key 'field' indicates model field's displayable name to be shown.
				else if(array_key_exists('field', $column)){
					$name = $this->model->getLabel($column['field']);
					$cell = Html::th($name, $thisOptions);
				}
			}
			$head .= $cell;
		}
		$head = Html::tr($head, $headRowOptions);
		$head = Html::thead($head);

		// Loop throught rules of each column and construct table body.
		$body = '';
		$size = sizeof($this->rowData);
		for($i = 0; $i < $size; $i++){
			$row = '';
			foreach($this->columns as $column){
				// If column is not an array, it refers to a model field.
				if(!is_array($column)){
					$row .= Html::td($this->rowData[$i][$column], $bodyCellOptions);
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
						$row .= Html::td($data, $bodyCellOptions);
					}
					// Pure HTML column.
					else if(array_key_exists('html', $column)){
						// Create string replacement table from rowData.
						$replace = [];
						foreach($this->rowData[$i] as $key => $value){
							$replace['{' . $key . '}'] = $value;
						}
						// In the attributes array, replace each rowData reference of {valuename} with referred value.
						array_walk_recursive($column['html']['params'], function(&$attribute, $key, $replace){
							$attribute = strtr($attribute, $replace);
						}, $replace);

						$html = call_user_func_array(array('Html', $column['html']['element']), $column['html']['params']);
						$row .= Html::td($html, $bodyCellOptions);
					}
				}
			}
			$body .= Html::tr($row, $bodyRowOptions);
		}
		$body = Html::tbody($body);

		return Html::table($head . $body, $tableOptions);
	}

}
?>
