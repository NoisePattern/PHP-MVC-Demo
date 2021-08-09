<?php

abstract class Model {
	protected $db;
	protected $errors = [];
	protected $ignoreFields = [];

	public function __construct(){
		$this->db = Database::dbConnect();
	}

	/**
	 * Name of the associated table in DB.
	 *
	 * @return string Returns table name.
	 */
	abstract function tableName();

	/**
	 * Name of DB table's primary key.
	 *
	 * @return string Name of primary key.
	 */
	abstract function getPrimaryKey();

	/**
	 * Names of fields in DB that will be matched to model. Each name must be identical to a model variable name defined above.
	 *
	 * @return array Returns an array containing DB field names.
	 */
	abstract function fields();

	/**
	 * Displayable names for model variables. Each array key must be identical to a model variable name defined above. Model variables
	 * that do not have label will be displaying their variable name in the form instead.
	 *
	 * @return array Returns an associative array containing displayable names for model variables.
	 */
	abstract function labels();

	/**
	 * Validation rules for model variables. Each key must be identical to a model variable name defined above.
	 *
	 * @return array Returns an associative array containing validation rules.
	 */
	abstract function rules();

	/**
	 * Sets model's data to given values. Model value is set only if a variable with same name as array's key exists.
	 *
	 * @param array $data Associative array of data to be set to model.
	 */
	public function values($data){
		foreach($data as $key => $value){
			if(property_exists($this, $key)){
				$this->{$key} = $value;
			}
		}
	}

	/**
	 * Gets label of given DB field.
	 *
	 * @param string $field Name of the field.
	 * @return string Returns label defined in the model, or the field name if none has been defined.
	 */
	public function getLabel($field){
		return $this->labels()[$field] ?? $field;
	}

	/**
	 * Checks if model values are for DB insert by primary key's state. If it is set, model is doing an update; if not, create.
	 *
	 * @return boolean Returns true on create, false on update.
	 */
	public function isCreate(){
		return isset($this->{$this->getPrimaryKey()}) ? false : true;
	}

	/**
	 * Checks if model values are for DB update by primary key's state. If it is set, model is doing an update; if not, create.
	 *
	 * @return boolean Returns true on update, false on create.
	 */
	public function isUpdate(){
		return isset($this->{$this->getPrimaryKey()}) ? true : false;
	}

	/**
	 * Validates model's data according to model's rules.
	 *
	 * @return boolean Returns true if validation passed, otherwise returns false.
	 */
	public function validate(){
		// Loop through model's rules array.
		foreach($this->rules() as $field => $rules){
			// Take the value of field this rule is for.
			$value = $this->{$field};
			// Loop through all rules for this field.
			foreach($rules as $rule){
				$ruleType = $rule;
				// If the rule is an array, not string, read its type from first index.
				if(is_array($ruleType)){
					$ruleType = $ruleType[0];
				}

				// Rule: field is required.
				if($ruleType === 'required' && !$value){
					$this->addError($field, 'required', $rule);
				}
				// Rule: field value must validate as email.
				else if($ruleType === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)){
					$this->addError($field, 'email', $rule);
				}
				// Rule: string length comparisons.
				else if($ruleType === 'length'){
					// String has rule for minimum length.
					if(array_key_exists('min', $rule) && $rule['min'] > strlen($value)){
						$this->addError($field, 'min', $rule);
					}
					// String has rule for maximum length.
					if(array_key_exists('max', $rule) && $rule['max'] < strlen($value)){
						$this->addError($field, 'max', $rule);
					}
					// String has rule for exact length.
					if(array_key_exists('equal', $rule) && $rule['equal'] != strlen ($value)){
						$this->addError($field, 'equal', $rule);
					}
				}
				// Rule: field value must match comparison field's value.
				else if($ruleType === 'compare' && $value != $this->{$rule['field']}){
					$this->addError($field, 'compare', $rule);
				}
				// Rule: number comparisons.
				else if($ruleType === 'numeric'){
					// Value must validate to an integer number.
					if(array_key_exists('integer', $rule) && $rule['integer'] && filter_var($value, FILTER_VALIDATE_INT) === FALSE){
						$this->addError($field, 'integer', $rule);
					}
					// Value must validate to float number.
					else if((!array_key_exists('integer', $rule) || (array_key_exists('integer', $rule) && !$rule['integer'])) && filter_var($value, FILTER_VALIDATE_FLOAT) === FALSE){
						$this->addError($field, 'numeric', $rule);
					}
					// Value must be at least a minimum number.
					if(array_key_exists('min', $rule) && $rule['min'] > floatval($value)){
						$this->addError($field, 'numericmin', $rule);
					}
					// Value cannot be larger than a maximum number.
					if(array_key_exists('max', $rule) && $rule['max'] < floatval($value)){
						$this->addError($field, 'numericmax', $rule);
					}
				}
				// Rule: unique database entry.
				else if($ruleType === 'unique'){
					// Check for match in database.
					$found = $this->findOne(["$field" => "$value"]);
					if($found){
						$this->addError($field, 'unique', $rule);
					}

				}
				// Rule: on action only.
				else if($ruleType === 'on'){
					// If field is used on create only and request is not for create, field is not used.
					if($rule['action'] === 'create' && !$this->isCreate()){
						$this->ignoreFields[] = $field;
					}
					// IF field is used on update only and request is not for update, field is not used.
					else if($rule['action'] === 'update' && !$this->isUpdate()){
						$this->ignoreFields[] = $field;
					}
				}
			}
		}
		return empty($this->errors);
	}

	/**
	 * Constructs an error message on failed validation rule.
	 *
	 * @param string $field The field on whose data the validation error happened on.
	 * @param string $ruleType Name of the rule the error happened on.
	 * @param array $rule Rule that did not validate.
	 */
	public function addError($field, $ruleType, $rule){
		$fieldLabel = $this->getLabel($field);
		// If rule has custom message attached, use it instead of constructing a standard message.
		if(is_array($rule) && array_key_exists('message', $rule)){
			$this->errors[$field][] = $rule['message'];
		}
		else if($ruleType === 'required'){
			$this->errors[$field][] = $fieldLabel . ' is required.';
		}
		else if($ruleType === 'email'){
			$this->errors[$field][] = 'Email address must be valid.';
		}
		else if($ruleType === 'min'){
			$this->errors[$field][] = $fieldLabel . ' must be at least ' . $rule['min'] . ' characters.';
		}
		else if($ruleType === 'max'){
			$this->errors[$field][] = $fieldLabel . ' cannot be longer than ' . $rule['max'] . ' characters.';
		}
		else if($ruleType === 'equal'){
			$this->errors[$field][] = $fieldLabel . ' must be ' . $rule['equal'] . ' characters.';
		}
		else if($ruleType === 'compare'){
			$this->errors[$field][] = $fieldLabel . ' and ' . $this->getLabel($rule['field']) . ' do not match.';
		}
		else if($ruleType === 'integer'){
			$this->errors[$field][] = $fieldLabel . ' must be an integer.';
		}
		else if($ruleType === 'numeric'){
			$this->errors[$field][] = $fieldLabel . ' must be a number.';
		}
		else if($ruleType === 'numericmin'){
			$this->errors[$field][] = 'Smallest allowed value for ' . $fieldLabel . ' is ' . $rule['min'] . '.';
		}
		else if($ruleType === 'numericmax'){
			$this->errors[$field][] = 'Largest allowed value for ' . $fieldLabel . ' is ' . $rule['max'] . '.';
		}
		else if($ruleType === 'unique'){
			$this->errors[$field][] = 'This ' . strtolower($fieldLabel) . ' is already in use.';
		}
	}

	/**
	 * Gets the first error set to given field.
	 *
	 * @param string $field DB name of the field.
	 * @return string|boolean Returns an error string or false if no error was set.
	 */
	public function getError($field){
		return $this->errors[$field][0] ?? false;
	}

	/**
	 * Saves model content to database as a new entry.
	 *
	 * @return boolean Returns true if insert succeeded, otherwise returns false.
	 */
	public function save(){
		// If primary key is set in model, run an update instead.
		$primaryKey = $this->getPrimaryKey();
		if(isset($this->{$primaryKey})){
			return $this->update();
		}
		$this->beforeSave();
		$tableName = $this->tableName();
		$fields = $this->fields();
		// Remove fields set as ignored by rules validation.
		$fields = $this->removeIgnored($fields);
		// Create PDO placeholders.
		$params = array_map(fn($name) => ':'.$name, $fields);
		// Populate statement by imploding the arrays.
		$statement = $this->db->prepare('INSERT INTO ' . $tableName . ' ( ' . implode(', ', $fields) . ') VALUES (' . implode(', ', $params) . ')');
		// Bind values to placeholders.
		foreach($fields as $field){
			$statement->bindValue(':' . $field, $this->{$field});
		}
		if($statement->execute()){
			$this->afterSave();
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Updates model content to database.
	 *
	 * @return boolean Returns true if update succeeded, otherwise return false.
	 */
	public function update(){
		$this->beforeSave();
		$tableName = $this->tableName();
		$fields = $this->fields();
		// Remove fields set as ignored by rules validation.
		$fields = $this->removeIgnored($fields);
		echo $this->updated;
		// Create PDO placeholders.
		$params = implode(', ', array_map(fn($name) => $name  . ' = :' . $name, $fields));
		$primaryKey = $this->getPrimaryKey();
		$where = $primaryKey . ' = :' . $primaryKey;
		$statement = $this->db->prepare('UPDATE ' . $tableName . ' SET ' . $params . ' WHERE ' . $where);
		// Bind values to placeholders.
		foreach($fields as $field){
			$statement->bindValue(':' . $field, $this->{$field});
		}
		$statement->bindValue(':' . $primaryKey, $this->{$primaryKey});
		if($statement->execute()){
			$this->afterSave();
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Deletes a row from DB where primary key value matches given id value.
	 *
	 * @return boolean Returns true if delete succeeded, otherwise returns false.
	 */
	public function delete($id){
		$tableName = $this->tableName();
		$primaryKey = $this->getPrimaryKey();
		$statement = $this->db->prepare('DELETE FROM ' . $tableName . ' WHERE ' . $primaryKey . ' =:' . $primaryKey . ' LIMIT 1');
		$statement->bindValue(':' . $primaryKey, $id);
		if($statement->execute()){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Removes keys from fields array that have been set as to be ignored by rules validation.
	 *
	 * @param array $fields Array of field names, as retrieved from target model.
	 * @return array Array of field names with ignored fields removed.
	 */
	public function removeIgnored($fields){
		foreach($this->ignoreFields as $ignore){
			if(($key = array_search($ignore, $fields)) !== false){
				unset($fields[$key]);
			}
		}
		return $fields;
	}

	/**
	 * Counts number of rows matching query criteria.
	 *
	 * @param array $condition query conditions as key-value pairs.
	 */
	public function count($condition = []){
		$tableName = $this->tableName();
		$where = '';
		if(!empty($condition)){
			$fields = array_keys($condition);
			$where = implode(" AND ", array_map(fn($field) => $field . ' = :'.$field, $fields));
		}
		$sql = 'SELECT COUNT(*) FROM ' . $tableName;
		if($where !== '') $sql .= ' WHERE ' . $where;
		$statement = $this->db->prepare($sql);
		foreach($condition as $key => $value){
			$statement->bindValue(':'.$key, $value);
		}
		$statement->execute();
		return $statement->fetchColumn();
	}

	/**
	 * Queries the database for a single result, either by primary key or by array of search values.
	 *
	 * @param mixed $condition Either a single value for primary key search or an associative array of search parameters for the where clause.
	 * @return object The query result as object.
	 */
	public function findOne($condition){
		$this->beforeFind();
		$tableName = $this->tableName();
		if(is_array($condition)){
			$fields = array_keys($condition);
			$sql = implode(" AND ", array_map(fn($field) => $field . ' = :'.$field, $fields));
		} else {
			$primary = $this->getPrimaryKey();
			$sql = $primary . ' = :' . $primary;
		}
		$statement = $this->db->prepare('SELECT * FROM ' . $tableName . ' WHERE ' . $sql);
		if(is_array($condition)){
			foreach($condition as $key => $value){
				$statement->bindValue(':'.$key, $value);
			}
		} else {
			$statement->bindValue(':' . $primary, $condition);
		}
		$statement->execute();
		$result = $statement->fetchObject();
		$this->afterFind($result);
		return $result;
	}

	/**
	 * Queries the database for multiple results that match the conditions, and applies given additional parameters to query.
	 *
	 * @param array $condition An associative array of search parameters for the where clause. Set empty array for no conditions.
	 * @param array $params Additional query params array. Supports:
	 * - orderBy: an array where first entry is the column name to order by, and second value is direction, either ASC or DESC string.
	 * - limit: The limit value.
	 * - offset: The offset value.
	 * @return array Returns a result set.
	 */
	public function findAll($condition, $params){
		$this->beforeFind();
		$tableName = $this->tableName();
		$sql = 'SELECT * FROM ' . $tableName;
		if(!empty($condition)){
			$fields = array_keys($condition);
			$sql .= ' WHERE ' . implode(" AND ", array_map(fn($field) => $field . ' = :'.$field, $fields));
		}
		// If there is orderBy param.
		if(array_key_exists('orderBy', $params)){
			// Make sure the column exists in model and direction is ASC or DESC
			if(property_exists($this, $params['orderBy'][0]) ){
				$sql .= ' ORDER BY ' . $params['orderBy'][0];
				if(strtolower($params['orderBy'][1]) === 'asc') $sql .= ' ASC';
				else if(strtolower($params['orderBy'][1]) === 'desc') $sql .= ' DESC';
			}
		}
		// If there is limit param.
		if(array_key_exists('limit', $params) && $params['limit'] > 0){
			$sql .= ' LIMIT :limit';
			// If there is offset param.
			if(array_key_exists('offset', $params)){
				$sql .= ' OFFSET :offset';
			}
		}
		$statement = $this->db->prepare($sql);
		foreach($condition as $key => $value){
			$statement->bindValue(':'.$key, $value);
		}
		if(array_key_exists('limit', $params) && $params['limit'] > 0){
			$statement->bindValue(':limit', $params['limit'], PDO::PARAM_INT);
			if(array_key_exists('offset', $params)){
				$statement->bindValue(':offset', $params['offset'], PDO::PARAM_INT);
			}
		}
		$statement->execute();
		$result = $statement->fetchAll();
		$this->afterFind($result);
		return $result;
	}

	/**
	 * Pre-save operations. If model data passes validation, this action runs before DB insert or update.
	 */
	public function beforeSave(){
	}

	/**
	 * Post-save operations. If model successfully inserted or updated, this action runs.
	 */
	public function afterSave(){
	}

	/**
	 * Pre-find operations. Runs before a select query is executed.
	 */
	public function beforeFind(){
	}

	/**
	 * Post-find operations. Runs after a select query has been executed.
	 *
	 * @param array $result The result array of a fetch operation.
	 */
	public function afterFind(&$result){
	}
}
?>