<?php

require_once 'Vertex.class.php';

class Vector {
	private $_x;
	private $_y;
	private $_z;
	private $_w; 
	private $_norm;

	public static $verbose = False;


	public function getX() { return $this->_x; }
	public function getY() { return $this->_y; }
	public function getZ() { return $this->_z; }
	public function getW() { return $this->_w; }
	public function getNorm() { return $this->_norm; }

	public function magnitude() 
	{
		return sqrt(($this->_x * $this->_x) + ($this->_y * $this->_y) + ($this->_z * $this->_z));
	}

	public function normalize() 
	{
		return new Vector ( array( 'dest' => new Vertex ( array(
			'x' => ($this->_x / $this->_norm),
			'y' => ($this->_y / $this->_norm),
			'z' => ($this->_z / $this->_norm)
			) ) ) );
	}
	public function add( $vect ) 
	{
		return new Vector ( array( 'dest' => new Vertex ( array(
			'x' => $this->_x + $vect->getX(),
			'y' => $this->_y + $vect->getY(),
			'z' => $this->_z + $vect->getZ(),
			'w' => 0,
			) ) ) );
	}
	public function sub( $vect ) 
	{
		return new Vector ( array( 'dest' => new Vertex ( array(
			'x' => $this->_x - $vect->getX(),
			'y' => $this->_y - $vect->getY(),
			'z' => $this->_z - $vect->getZ(),
			'w' => 0,
			) ) ) );
	}
	public function opposite() 
	{
		return new Vector ( array( 'dest' => new Vertex ( array(
			'x' => -($this->_x),
			'y' => -($this->_y),
			'z' => -($this->_z),
			'w' => 0,
			) ) ) );
	}

	public function scalarProduct( $k )
	{
		return new Vector ( array( 'dest' => new Vertex ( array(
			'x' => ($this->_x) * $k,
			'y' => ($this->_y) * $k,
			'z' => ($this->_z) * $k,
			'w' => 0,
			) ) ) );
	}

	public function dotProduct( $vect )
	{
		return $this->_x * $vect->getX() + $this->_y * $vect->getY() + $this->_z * $vect->getZ();
	}

	public function cos( Vector $vect )
	{
		return ($this->dotProduct( $vect) / ($this->_norm * $vect->getNorm()));
	}

	public function crossProduct( $vect )
	{
		return new Vector ( array( 'dest' => new Vertex ( array(
			'x' => $this->_y * $vect->getZ() - $this->_z * $vect->getY(),
			'y' => $this->_z * $vect->getX() - $this->_x * $vect->getZ(),
			'z' => $this->_x * $vect->getY() - $this->_y * $vect->getX(),
			'w' => 0,
			) ) ) );
	}

	public static function doc()
	{
		return file_get_contents("./Vector.doc.txt");
	}

	private function _invalid_arguments()
	{
		print( "Invalid arguments for Vector instance." . PHP_EOL);
		exit(1);
	}

	function __construct( array $kwargs )
	{
		if (isset($kwargs['dest']))
		{
			$dest = $kwargs['dest'];
			$this->_x = $dest->getX();
			$this->_y = $dest->getY();
			$this->_z = $dest->getZ();
			$this->_w = 0;
			if (isset($kwargs['orig']))
			{
				$orig = $kwargs['orig'];
				$this->_x -= $orig->getX();
				$this->_y -= $orig->getY();
				$this->_z -= $orig->getZ();
			}
			else
				$orig = new Vertex ( array(
					'x' => 0,
					'y' => 0,
					'z' => 0,
					'w' => 1,
				) );
			$this->_norm = $this->magnitude();
		}
		else
			_invalid_arguments();
		if ( self::$verbose === True )
			print( $this . " constructed" . PHP_EOL);
	}
	function __destruct()
	{
		if ( self::$verbose === True )
				print( $this . " destructed" . PHP_EOL);
	}
	function __toString()
	{
		return sprintf("Vector( x:%.2f, y:%.2f, z:%.2f, w:%.2f )",
			$this->getX(), $this->getY(), $this->getZ(), $this->getW());
	}
}
?>