<?php

require_once 'Color.class.php';

class Vertex {
	private $_x;
	private $_y;
	private $_z;
	private $_w;
	private $_color;

	public static $verbose = False;


	public function getX() { return $this->_x; }
	public function getY() { return $this->_y; }
	public function getZ() { return $this->_z; }
	public function getW() { return $this->_w; }
	public function getColor() { return $this->_color; }

	public function setX( $v ) { $this->_x = $v; }
	public function setY( $v ) { $this->_y = $v; }
	public function setZ( $v ) { $this->_z = $v; }
	public function setW( $v ) { $this->_w = $v; }
	public function setColor( $v ) { return $this->_color = $v; }

	public static function doc()
	{
		return file_get_contents("./Vertex.doc.txt");
	}

	private function _invalid_arguments()
	{
		print( "Invalid arguments for Vertex instance." . PHP_EOL);
		exit(1);
	}

	function __construct( array $kwargs )
	{
		if (isset($kwargs['x']) && isset($kwargs['y']) && isset($kwargs['z']))
		{
			$this->_x = $kwargs['x'];
			$this->_y = $kwargs['y'];
			$this->_z = $kwargs['z'];
			$this->_w = (isset($kwargs['w'])) ? $kwargs['w'] : 1;
			$this->_color = (isset($kwargs['color'])) ? $kwargs['color'] :
				new Color( array ( 'rgb' => 0xffffff ) );
		}
		else
			_invalid_arguments();
		if ( Vertex::$verbose === True )
			print( $this . " constructed" . PHP_EOL);
	}
	function __destruct()
	{
		if ( Vertex::$verbose === True )
				print( $this . " destructed" . PHP_EOL);
	}
	function __toString()
	{
		$str = sprintf("Vertex( x: %.2f, y: %.2f, z:%.2f, w:%.2f",
			$this->_x, $this->_y, $this->_z, $this->_w);
		if ( Vertex::$verbose === True )
			$str .= ", ".$this->_color;
		return $str." )";
	}
}
?>