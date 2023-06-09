<?php


declare(strict_types=1);

namespace skyblock\traits;

use Exception;

trait AetherSingletonTrait{
	/** @var self|null */
	protected static $instance = null;

	private static function make() : static{
		return new self;
	}

	public static function getInstance() : static{
		if(self::$instance === null){
			self::$instance = self::make();
		}
		return self::$instance;
	}

	public static function setInstance(self $instance) : void{
		if(self::$instance !== null){
			throw new Exception("initiliaed twice");
		}
		
		self::$instance = $instance;
	}

	public static function reset() : void{
		self::$instance = null;
	}
}
