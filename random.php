<?php
/**
* Random number generation methods for PHP
* @author Matt Carter <m@ttcarter.com>
*/
class Random {
	var $method;

	function __construct() {
		$this->method = $this->GetPrefered(array('openssl', 'mt_rand', 'rand'));
	}

	/**
	* Set the default method to use when calling Rand()
	* @param string $method The mettod Rand() should use if none is explicitly specified
	* @see Rand()
	*/
	function Method($method) {
		$this->method = $method;
	}

	/**
	* Figure out the prefered random generation method when passed an array of possible methods
	* @param array $method An array (in prefered order) of random methods to try.
	* @return string The first suitable random generation method that is available
	*/
	function GetPrefered($methods) {
		foreach ($methods as $method)
			if ($this->IsAvailable($method))
				return $method;
		return FALSE;
	}

	/**
	* Returns a bool indicating if a method is available
	* @param string $method The method to determine availablity
	* @return bool Whether the given $method is available on this system
	*/
	function IsAvailable($method) {
		switch ($method) {
			case 'rand':
				return function_exists('rand');
			case 'mt_rand':
				return function_exists('mt_rand');
			case 'openssl':
				return function_exists('openssl_random_pseudo_bytes');
		}
	}

	/**
	* Actually generate a random number
	* If no $min / $max is given the output number will be a float value between 0 and 1
	* @param int $min Optional minimum value (must be used with $max)
	* @param int $max Optional maximum value (must be used with $min)
	* @param string $method Optional random generation method to use. If unspcified $this->method is used
	* @return float|int If $min && $max are specified a random number in that range, if unspecified a random float value between 0 and 1
	*/
	function Rand($min = null, $max = null, $method = null) {
		if ($min && !$max) {
			trigger_error('Cannot specify Rand() with $min but not $max');
			return FALSE;
		} elseif ($min && $max && $max <= $min) {
			trigger_error('$max cannot be equal or more than $min on Rand()');
			return FALSE;
		}
		if (!$method)
			$method = $this->method;

		switch ($method) {
			case 'rand':
				return $min && $max ? rand($min, $max) : rand() / getrandmax();
			case 'mt_rand':
				return $min && $max ? mt_rand($min, $max) : mt_rand() / mt_getrandmax();
			case 'openssl':
				// Thanks to Tyler Larson for his example (http://www.php.net/manual/en/function.openssl-random-pseudo-bytes.php#93079)
				if (!$min || !$max) {
					$min = 0;
					$max = PHP_INT_MAX;
					$convert = 1;
				} else
					$convert = 0;
				$range = $max - $min;
				$length = (int) (log($range,2) / 8) + 1;
				$r = $min + (hexdec(bin2hex(openssl_random_pseudo_bytes($length,$s))) % $range);
				return $convert ? abs($r / PHP_INT_MAX) : $r;
		}
	}

	/**
	* Generate and output a noise map using the selected $method
	* This can be used as a quick indication of just how random the different methods actually are
	* @param string $method Optional random generation method to use. If unspecified $this->method will be used.
	* @param int $width Optional width of the noise map
	* @param int $height Optional height of the noise map
	*/
	function NoiseMap($method = null, $width = 500, $height = 500) {
		// Thanks to cyrax21 for his example (http://www.php.net/manual/en/function.mt-rand.php#107738)
		header("Content-type: image/png");
		$img = imagecreatetruecolor($width, $height);
		$ink = imagecolorallocate($img, 255,255,255);

		for($i=0; $i<$width; $i++)
			for($j=0; $j<$height; $j++)
				imagesetpixel($img, $this->Rand(1,$width), $this->Rand(1,$height), $ink);

		imagepng($img);
		imagedestroy($img); 
	}
}
