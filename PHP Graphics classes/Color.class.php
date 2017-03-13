<?php
class Color {
	public $red;
	public $green;
	public $blue;

	public static $verbose = False;
	

	public static function doc()
	{
		return file_get_contents("./Color.doc.txt");
	}

	private function _invalid_arguments()
	{
		print( "Invalid arguments for Color instance." . PHP_EOL);
		exit(1);
	}

	function __construct( array $kwargs )
	{
		if (isset($kwargs['rgb']))
		{
			$color = intval($kwargs['rgb']);
			$this->blue = $color & 255;
			$this->green = ($color >> 8) & 255;
			$this->red = ($color >> 16) & 255;
		}
		else if (isset($kwargs['red']) && isset($kwargs['green']) && isset($kwargs['blue']))
		{
			$this->blue = intval($kwargs['blue'], 10);
			$this->green = intval($kwargs['green'], 10);
			$this->red = intval($kwargs['red'], 10);
		}
		else
			_invalid_arguments();
		if ( Color::$verbose === True )
			print( $this . " constructed." . PHP_EOL);
	}
	function __destruct()
	{
		if ( Color::$verbose === True )
				print( $this . " destructed." . PHP_EOL);
	}
	function __toString()
	{
		return sprintf("Color( red: %3d, green: %3d, blue: %3d )",
			$this->red, $this->green, $this->blue);
	}

	public function add( Color $color )
	{
		return new Color ( array(
			'red' => $this->red + $color->red,
			'green' => $this->green + $color->green,
			'blue' => $this->blue + $color->blue) );
	}
	public function sub( Color $color )
	{
		return new Color ( array(
			'red' => $this->red - $color->red,
			'green' => $this->green - $color->green,
			'blue' => $this->blue - $color->blue) );
	}
	public function mult( $num )
	{
		return new Color ( array(
			'red' => $this->red * $num,
			'green' => $this->green * $num,
			'blue' => $this->blue * $num) );
	}
}
?>