<?php
/**
 * Created by PhpStorm.
 * User: Younes
 * Date: 14/12/2015
 * Time: 11:09
 */

namespace Orm\Orm;

use Orm\Interfaces\QueryInterface;

class QueryManager implements  QueryInterface{

	private function varName($var) {
		foreach($GLOBALS as $var_name => $value) {
			if ($value === $var) {
				return $var_name;
			}
		}

		return false;
	}


	public function select($table , $column = '*'){
		$query = "SELECT $column FROM $table";
		return $query;

	}
	public function update(){

	}
	public function delete(){
		$query = "DELETE FROM ".$this->getTable()." WHERE id=".$this->getId();
		$connexion = Orm::getConnexion();
		$result = $connexion->query($query);
	}
	public function insert($methods){

		$query = "INSERT INTO ".$this->getTable()." VALUE (''";
		foreach($methods as $name => $method){
			$query .= ", '".$this->$method()."'";
		}
		$query .= ")";
		return $query;
	}
	public function count(){


	}

	public function persist(){

		$table = $this->getTable();
		$connexion = Orm::getConnexion();
		$columns = $connexion->query("SELECT table_name, column_name, data_type FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$table' AND table_schema = 'orm'")->fetchAll();
		$methods = [];

		foreach( $columns as $e){
			if($e['column_name'] == 'id'){
				continue;
			}else{
				$methods[$e['column_name']] = 'get'.ucfirst($e['column_name']);
			};
		}

		$query = $this->insert($methods, $this);
		$connexion->query($query);
		$result = $connexion->lastInsertId();
		$this->setId($result);

	}

	public function countTable($table){
	}
 }