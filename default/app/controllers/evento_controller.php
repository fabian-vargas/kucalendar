<?php
class EventoController extends ScaffoldController{
public $model = 'evento';

	public function kucalend($page=1,$valor=NULL,$ppage=100)
	{

		$this->data = (new Evento)->getcalend();
		

    }
}