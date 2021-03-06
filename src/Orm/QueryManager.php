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

	public function select($table , $column = '*'){
		$query = "SELECT $column FROM $table";
		return $query;

	}
	public function update($methods){

		$query = "UPDATE ".$this->getTable()." SET";

		$numItems = count($methods);
		$i = 0;

		foreach($methods as $name => $method){

			if(++$i === $numItems){
				$query .= " `$name`=\"".$this->$method()."\" ";
			}else{
				$query .= " `$name`=\"".$this->$method()."\", ";
			}

		}
		$query .= "WHERE `id`=".$this->getId()."; ";

		return $query;
	}
	public function delete(){
		$query = "DELETE FROM ".$this->getTable()." WHERE id=".$this->getId().";";
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
	public static function countItems($self){
		$connexion = Orm::getConnexion();
		$query = "SELECT COUNT(*) FROM ".$self->getTable().";";
		$result = $connexion->query($query)->fetch();
		return $result[0];

	}

	public function persist(){

		$table = $this->getTable();
		$connexion = Orm::getConnexion();
		$columns = $connexion->query("SELECT table_name, column_name, data_type FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$table' AND table_schema = 'orm';")->fetchAll();
		$methods = [];

		foreach( $columns as $column){
			if($column['column_name'] == 'id'){
				continue;
			}else{
				$methods[$column['column_name']] = 'get'.ucfirst($column['column_name']);
			};
		}

		if($this->getId() != null){
			$query = $this->update($methods, $this);
			$connexion->query($query);
		}else{
			$query = $this->insert($methods, $this);
			$connexion->query($query);
			$result = $connexion->lastInsertId();
			$this->setId($result);
		}

	}

	public function countTable($table){

	}
 }