<?php
namespace App\Services;

class Direr{

public $appDir;
public $wwwDir;


	public function __construct($appDir, $wwwDir)
	{
		$this->appDir = $appDir;
		$this->wwwDir = $wwwDir;
	}
}
