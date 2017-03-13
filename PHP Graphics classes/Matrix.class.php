<?php

require_once 'Vertex.class.php';
require_once 'Vector.class.php';

class Matrix {
	const IDENTITY = "IDENTITY";
	const SCALE = "SCALE preset"; 
	const RX = "Ox ROTATION preset";
	const RY = "Oy ROTATION preset";
	const RZ = "Oz ROTATION preset";
	const TRANSLATION = "TRANSLATION preset";
	const PROJECTION = "PROJECTION preset";
	public $matrix = array(array(1, 0, 0, 0), array(0, 1, 0, 0), array(0, 0, 1, 0), array(0, 0, 0, 1) );

	public static $verbose = False;


	public function getX() { return $this->_x; }
	public function getY() { return $this->_y; }
	public function getZ() { return $this->_z; }
	public function getW() { return $this->_w; }

	public static function doc()
	{
		return file_get_contents("./Matrix.doc.txt");
	}

	private function _invalid_arguments()
	{
		print( "Invalid arguments for Matrix instance." . PHP_EOL);
		exit(1);
	}

	public function mult( $mtrx )
	{
		$result = array(array(1, 0, 0, 0), array(0, 1, 0, 0), array(0, 0, 1, 0), array(0, 0, 0, 1) );
		foreach ( $this->matrix as $row => $vect) 
		{
			foreach ($vect as $col => $val)
				$result[$row][$col] =
					$this->matrix[$row][0] * $mtrx->matrix[0][$col] +
					$this->matrix[$row][1] * $mtrx->matrix[1][$col] +
					$this->matrix[$row][2] * $mtrx->matrix[2][$col] +
					$this->matrix[$row][3] * $mtrx->matrix[3][$col];
		}
		foreach ( $this->matrix as $row => $vect) 
		{
			foreach ($vect as $col => $val)
				$this->matrix[$row][$col] = $result[$row][$col];
		}
		return $this;
	}

	public function transformVertex ( $vtx )
	{
		$x = $vtx->getX() * $this->matrix[0][0] + $vtx->getY() * $this->matrix[0][1] +
			$vtx->getZ() * $this->matrix[0][2] + $this->matrix[0][3];
		$y = $vtx->getX() * $this->matrix[1][0] + $vtx->getY() * $this->matrix[1][1] +
			$vtx->getZ() * $this->matrix[1][2] + $this->matrix[1][3];
		$z = $vtx->getX() * $this->matrix[2][0] + $vtx->getY() * $this->matrix[2][1] +
			$vtx->getZ() * $this->matrix[2][2] + $this->matrix[2][3];
		$w = $vtx->getX() * $this->matrix[3][0] + $vtx->getY() * $this->matrix[3][1] +
			$vtx->getZ() * $this->matrix[3][2] + $this->matrix[3][3];
		return (new Vertex(['x' => $x / $w, 'y' => $y / $w, 'z' => $z / $w]));
	} 

	private function _scl_mtrx ( $sc )
	{
		$this->matrix[0][0] = $sc;
		$this->matrix[1][1] = $sc;
		$this->matrix[2][2] = $sc;
	}

	private function _rot_mtrx ( $ang, $type )
	{
		if ($type == self::RX)
		{
			$this->matrix[1][1] = cos($ang);
			$this->matrix[1][2] = -sin($ang);
			$this->matrix[2][1] = sin($ang);
			$this->matrix[2][2] = cos($ang);
		}
		else if ($type == self::RY)
		{
			$this->matrix[0][0] = cos($ang);
			$this->matrix[0][2] = sin($ang);
			$this->matrix[2][0] = -sin($ang);
			$this->matrix[2][2] = cos($ang);
		}
		else if ($type == self::RZ)
		{
			$this->matrix[0][0] = cos($ang);
			$this->matrix[0][1] = -sin($ang);
			$this->matrix[1][0] = sin($ang);
			$this->matrix[1][1] = cos($ang);
		}
	}

	private function _trns_mtrx ( $vtc )
	{
		$this->matrix[0][3] += $vtc->getX();
		$this->matrix[1][3] += $vtc->getY();
		$this->matrix[2][3] += $vtc->getZ();
	}

	private function _opengl_prog_mtrx(  $fov, $ratio, $near, $far )
	{
		$scale = tan(deg2rad($fov * 0.5)) * $near;
		$right = $ratio * $scale;
		$left = -$right;
		$top = $scale;
		$bottom = -$top;
		self::_OpenGLFrustrum($left, $right, $bottom, $top, $near, $far);
	}

	private function _OpenGLFrustrum($left, $right, $bottom, $top, $near, $far)
	{
		$this->matrix[0][0] = (2 * $near) / ($right - $left);
		$this->matrix[0][1] = 0;
		$this->matrix[0][2] = ($right + $left) / ($right - $left);
		$this->matrix[0][3] = 0;

		$this->matrix[1][0] = 0;
		$this->matrix[1][1] = (2 * $near) / ($top - $bottom);
		$this->matrix[1][2] = ($top + $bottom) / ($top - $bottom);
		$this->matrix[1][3] = 0;

		$this->matrix[2][0] = 0;
		$this->matrix[2][1] = 0;
		$this->matrix[2][2] = -(($far + $near) / ($far - $near));
		$this->matrix[2][3] = -((2 * $far * $near) / ($far - $near));

		$this->matrix[3][0] = 0;
		$this->matrix[3][1] = 0;
		$this->matrix[3][2] = -1;
		$this->matrix[3][3] = 0;
	}

	function __construct( array $kwargs )
	{
		if (isset($kwargs['preset']))
		{
			$preset = $kwargs['preset'];
			if ( self::$verbose == TRUE )
				print('Matrix ' . $preset . ' instance constructed' . PHP_EOL);
			if ( $kwargs['preset'] == self::IDENTITY )
				return ;
			else if ($preset == self::SCALE && isset($kwargs['scale']))
				$this->_scl_mtrx($kwargs['scale']);
			else if (($preset == self::RX || $preset == self::RY || $preset == self::RZ) && isset($kwargs['angle']))
				$this->_rot_mtrx($kwargs['angle'], $preset);
			else if ($preset == self::TRANSLATION && isset($kwargs['vtc']))
				$this->_trns_mtrx($kwargs['vtc']);
			else if ($preset == self::PROJECTION && isset($kwargs['fov']) && isset($kwargs['ratio'])
				&& isset($kwargs['near']) && isset($kwargs['far']))
				$this->_opengl_prog_mtrx($kwargs['fov'], $kwargs['ratio'], $kwargs['near'], $kwargs['far']);
			else
			_invalid_arguments();
		}
		else
			_invalid_arguments();
	}
	function __destruct()
	{
		if ( self::$verbose === True )
				print('Matrix instance destructed' . PHP_EOL);
	}
	function __toString()
	{
		return sprintf('M | vtcX | vtcY | vtcZ | vtxO' . PHP_EOL
			. '-----------------------------' . PHP_EOL
			. 'x | %.2f | %.2f | %.2f | %.2f' . PHP_EOL
			. 'y | %.2f | %.2f | %.2f | %.2f' . PHP_EOL
			. 'z | %.2f | %.2f | %.2f | %.2f' . PHP_EOL
			. 'w | %.2f | %.2f | %.2f | %.2f'
			, $this->matrix[0][0], $this->matrix[0][1], $this->matrix[0][2], $this->matrix[0][3],
			$this->matrix[1][0], $this->matrix[1][1], $this->matrix[1][2], $this->matrix[1][3],
			$this->matrix[2][0], $this->matrix[2][1], $this->matrix[2][2], $this->matrix[2][3],
			$this->matrix[3][0], $this->matrix[3][1], $this->matrix[3][2], $this->matrix[3][3]
			);
	}
}
?>