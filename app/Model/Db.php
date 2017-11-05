<?php	namespace Model;

	use \Helper\Db as Driver;
	use \PDO;
	use \Model\CustomException;

	class Db extends Base {
		
		/**
		 * Returns the list of items from table by conditions
		 * @param tableName string
		 * @param conditions array
		 * @param conditionsConcat string
		 * @param orderByField string 		field of the table
		 * @param orderDest string			ASC | DESC
		 * @param offset integer
		 * @param limit integer
		 */
		public static function getList($tableName, $conditions = [], $conditionsConcat = 'AND', $orderByField = 'id', $orderDest = 'ASC', $offset = 0, $limit = 10){
			list ($conditionsQuery, $conditionsParams) = self::processConditions($conditions, $conditionsConcat);
			$sqlQuery = "SELECT * FROM `" . $tableName . "` " . $conditionsQuery . " ORDER BY `" . $orderByField . "` " . $orderDest . " LIMIT " . $offset . ", " . $limit;
			$statement = self::executeQuery($sqlQuery, $conditionsParams);
			$list = $statement->fetchAll(PDO::FETCH_ASSOC);
			
			return $list;
		}
		
		/**
		 * Returns single row from table by conditions
		 * @param tableName string
		 * @param conditions array
		 * @param orderByField string 		field of the table
		 * @param orderDest string			ASC | DESC
		 * @param offset integer		row offset
		 */
		public static function getRow($tableName, $conditions = [], $orderByField = 'id', $orderDest = 'ASC', $offset = 0){
			list ($conditionsQuery, $conditionsParams) = self::processConditions($conditions);
			$sqlQuery = "SELECT * FROM `" . $tableName . "` " . $conditionsQuery . " ORDER BY `" . $orderByField . "` " . $orderDest . " LIMIT " . $offset . ", 1";
			$statement = self::executeQuery($sqlQuery, $conditionsParams);
			$row = $statement->fetch(PDO::FETCH_ASSOC);
			
			return $row;
		}
		
		/**
		 * Returns single field from table's row
		 * @param tableName string
		 * @param field string
		 * @param conditions array
		 * @param orderByField string 		field of the table
		 * @param orderDest string			ASC | DESC
		 * @param offset integer		row offset
		 */
		public static function getOne($tableName, $field, $conditions = [], $orderByField = 'id', $orderDest = 'ASC', $offset = 0){
			list ($conditionsQuery, $conditionsParams) = self::processConditions($conditions);
			$sqlQuery = "SELECT " . $field . " FROM `" . $tableName . "` " . $conditionsQuery . " ORDER BY `" . $orderByField . "` " . $orderDest . " LIMIT " . $offset . ", 1";
			$statement = self::executeQuery($sqlQuery, $conditionsParams);
			$row = $statement->fetch(PDO::FETCH_NUM);
			$value = $row[0];
			
			return $value;
		}
		
		/**
		 * Returns column of values from specified field
		 * @param tableName string
		 * @param field string
		 * @param conditions array
		 * @param orderByField string 		field of the table
		 * @param orderDest string			ASC | DESC
		 *
		 * return array
		 */
		public static function getColumn($tableName, $field, $conditions = [], $orderByField = 'id', $orderDest = 'ASC'){
			list ($conditionsQuery, $conditionsParams) = self::processConditions($conditions);
			$sqlQuery = "SELECT " . $field . " FROM `" . $tableName . "` " . $conditionsQuery . " ORDER BY `" . $orderByField . "` " . $orderDest;
			$statement = self::executeQuery($sqlQuery, $conditionsParams);
			$column = $statement->fetchColumn();
			
			return $column;
		}
		
		/**
		 *
		 * @param tableName string
		 * @param conditions array
		 *
		 * return integer
		 */
		public static function insertRow($tableName, $data = []){
			list ($dataQuery, $dataParams) = self::processConditions($data, ',', '');
			$sqlQuery = "INSERT INTO `" . $tableName . "` SET " . $dataQuery;
			$statement = self::executeQuery($sqlQuery, $dataParams);
			
			return Driver::getInstance()->lastInsertId();
		}
		
		/**
		 *
		 * @param tableName string
		 * @param conditions array
		 *
		 * return integer
		 */
		public static function updateRow($tableName, $data = [], $conditions = []){
			list ($dataQuery, $dataParams) = self::processConditions($data, ',', '');
			list ($conditionsQuery, $conditionsParams) = self::processConditions($conditions);
			$sqlQuery = "UPDATE `" . $tableName . "` SET " . $dataQuery . $conditionsQuery;
			$statement = self::executeQuery($sqlQuery, array_merge($dataParams, $conditionsParams));
			
			return $statement->rowCount();
		}
		
		/**
		 *
		 * @param tableName string
		 * @param conditions array
		 *
		 * return integer
		 */
		public static function deleteRow($tableName, $conditions = []){
			list ($conditionsQuery, $conditionsParams) = self::processConditions($conditions);
			$sqlQuery = "DELETE FROM `" . $tableName . "`" . $conditionsQuery;
			$statement = self::executeQuery($sqlQuery, array_merge($dataParams, $conditionsParams));
			
			return $statement->rowCount();
		}
		
		/**
		 * Runs manual query
		 * @param sqlQuery string
		 * @param sqlParams array
		 * @param returnResult boolean
		 */
		public static function runQuery($sqlQuery, $sqlParams = [], $returnResult = false){
			$statement = self::executeQuery($sqlQuery, $sqlParams);
			
			if ($returnResult)
				return $statement->fetchAll(PDO::FETCH_ASSOC);
		}
		
		/**
		 * Processes conditions array into SQL WHERE statement and its arguments
		 * @param conditions array
		 * @param concatOperator string		AND | ,
		 */
		private static function processConditions($conditions = [], $concatOperator = 'AND', $conditionsPrefix = 'WHERE'){
			$conditionsData = $conditionsParams = [];
			
			foreach ($conditions as $field => $fieldData) {
				$conditionsData[] = $field . ' '
					. (gettype($fieldData) == 'array' && isset($fieldData['compare']) ? $fieldData['compare'] : '=')
					. (gettype($fieldData) == 'array' && isset($fieldData['value']) && gettype($fieldData['value']) == 'array'
						? implode(' AND ', array_fill(0, sizeOf($fieldData['value']), '?'))
						: ' ?'
					);
				
				$conditionsParams = array_merge(
					$conditionsParams,
					gettype($fieldData) == 'array' && isset($fieldData['value']) ?
						(gettype($fieldData['value']) == 'array' ? $fieldData['value'] : [$fieldData['value']])
						: [$fieldData]
				);
			}
			
			return [
						($conditionsData ? ' ' . $conditionsPrefix : '') . ' ' . implode(' ' . $concatOperator . ' ', $conditionsData),
						$conditionsParams
					];
		}
		
		/**
		 * Executes sql query
		 * @param sqlQuery string
		 * @param sqlParams array
		 */
		private static function executeQuery($sqlQuery, $sqlParams){
			$statement = Driver::getInstance()->prepare($sqlQuery);
			
			try {
				$statement->execute($sqlParams);
				
				if (intval($statement->errorCode())) {
					list ($errorCode, $driverCode, $driverMessage) = $statement->errorInfo();
					throw new CustomException($driverMessage . ' (' . $sqlQuery . '<br /><pre>' . print_r(array_map(function($v){ return $v === null ? 'NULL' : $v; }, $sqlParams), 1) . '</pre>)', $driverCode);
				}
			} catch (CustomException $e) {
				$e->addNotificationAndReload();
			}
				
			return $statement;
		}
		
	}
	
?>