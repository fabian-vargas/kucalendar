<?php
class Evento extends ActiveRecord{

	public function initialize(){  

	}	

	public function getcalend(){  
		/**
		*faltaria filtrar x mes*/	
		return $this->find("order: fecha,hora_inicio asc");//"columns: $columns", "join: $join", "conditions: $conditions", "order: $order");//obtenemos todos los recursos

	}	
}	