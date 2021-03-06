<?php

namespace OSC\Village;

use Aedea\Core\Database\StdCollection;

class Collection extends StdCollection {
	
	public function __construct( $params = array() ){
		parent::__construct($params);
		
		$this->addTable('village', 'vl');
		$this->idField = 'vl.id';
		$this->setDistinct(true);
		
		$this->objectType = __NAMESPACE__ . '\Object';		
	}

	public function filterById( $arg ){
		$this->addWhere("vl.id = '" . (int)$arg. "' ");
	}

	public function filterByCommuneId( $arg ){
		$this->addWhere("vl.commune_id = '" . (int)$arg. "' ");
	}

	public function filterByName( $arg ){
		$this->addWhere("vl.name_en LIKE '%" . $arg. "%' ");
	}

	public function orderById($arg){
		$this->addOrderBy('vl.id', $arg);
	}

}
