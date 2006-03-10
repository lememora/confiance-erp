<?php

/* -----------------------------------
 * Encoded OurNumber Class
 * -----------------------------------
 *
 * Rafael Moraes
 *
 * OBS: The array input/output format must be:
 *
 * array ([0-1048575] => [0.00-10485.75], ...)
 *
 */

class OurNumber {
	
        /*  
	 * Hex Codify (16 bytes)
         *                 0123456789ABCDEF (Please Reserve 'X')
	 */
	var $hex_codify = "ZWTRQPNLKJHGFDCB";

	/*
	 * Pack Hex Code (32 bytes) (Please Reserve '0' and '-');
	 */
	var $hex_pack   = "abcdefghijklmnpqrstxwyz123456789";

	function OurNumber () {
		return true;
	}

	function encode ($a_data) 
	{
		$s_hex_qt = '';
		$s_hex_vl = '';
		$s_hex_rs = '';
		
		foreach ($a_data AS $i_qt=>$f_vl)
	       	{
			$s_hex_qt  = $this->codhx ($i_qt);
			$s_hex_vl  = $this->codhx ($f_vl * 100);
			$s_hex_rs .= $s_hex_qt . $s_hex_vl;
		}
		return $this->hxpack ($s_hex_rs);
	}

	function decode ($s_data)
	{
		$i_qt   = 0;
		$f_vl   = 0.00;
		$a_rs   = array ();
		$s_data = $this->hxupck ($s_data);
					
		for ($i=0; $i<strlen ($s_data); $i+=8)
		{
			$i_qt = $this->dcdhx (substr ($s_data, $i, 4));
			$f_vl = $this->dcdhx (substr ($s_data, $i + 4, 4));
			$a_rs [$i_qt] = number_format($f_vl / 100, 2);
		}
		return $a_rs;
	}

	function codhx ($i_dec) 
	{
		$s_hex = strtoupper (dechex ($i_dec));
		$s_hex = strtr ($s_hex, '0123456789ABCDEF', $this->hex_codify); 
		return $this->xfill ($s_hex, 4);
	}

	function dcdhx ($s_hex) 
	{
		$s_hex = str_replace ('X', '', $s_hex);
		$s_hex = strtr ($s_hex, $this->hex_codify, '0123456789ABCDEF');
		return hexdec (strtolower ($s_hex));
	}

	function xfill ($s_hex, $i_chars) 
	{
		$s_hex_rs = $s_hex;
		while (strlen ($s_hex_rs) < 4) $s_hex_rs .= 'X';
		return $s_hex_rs;
	}

	function hxpack ($s_hex)
       	{
		$s_uh     = '';
		$s_ph     = '';
		$s_packed = $s_hex;

		$s_packed = str_replace ('XX', '0', $s_packed);
		
		for ($i=0; $i<8; $i++)
		{
			$s_uh     = str_repeat ($this->hex_codify [$i], 2);
			$s_ph     = $this->hex_pack [$i];
			$s_packed = str_replace ($s_uh, $s_ph, $s_packed);
		}
		for ($i=0; $i<8; $i++)
		{
			$s_uh     = $this->hex_codify [$i] . "X";
			$s_ph     = $this->hex_pack   [($i + 8)];
			$s_packed = str_replace ($s_uh, $s_ph, $s_packed);
		}
		return $s_packed;
	}

	function hxupck ($s_hex) 
	{
		$s_uh     = '';
		$s_ph     = '';
		$s_upcked = $s_hex;
		
		for ($i=0; $i<8; $i++)
		{
			$s_ph     = $this->hex_pack [($i + 8)];
			$s_uh     = $this->hex_codify [$i] . "X";
			$s_upcked = str_replace ($s_ph, $s_uh, $s_upcked);
		}
		for ($i=0; $i<8; $i++)
		{
			$s_ph     = $this->hex_pack [$i];
			$s_uh     = str_repeat ($this->hex_codify [$i], 2);
			$s_upcked = str_replace ($s_ph, $s_uh, $s_upcked);
		}
		return str_replace ('0', 'XX', $s_upcked);
	}
}

/*
 * Simulations
 */ 
/*   
$a_test = array (0    => 35.16,

	         50   => 2.99, 100  => 2.85, 250  => 2.72, 500   => 2.65,
                 1000 => 2.44, 2000 => 2.25, 5000 => 2.02, 10000 => 1.80);

// 50,2.99,100,2.85,250,2.72,500,2.65,1000,2.44,2000,2.25,5000,2.02,10000,1.80
// RT0WTxNQ0byBH0biWBmWZsRCrBQ0LDiCW0WRiFH0TLWZGQ0 (-46%!)

$o_ON = new OurNumber ();
$s    = $o_ON->encode ($a_test);
$a    = $o_ON->decode ($s);

echo "CODIFIED: ";
echo $s . "<br>\n";

echo "DECODIFIED: ";
echo "<pre>";
print_r ($a);
echo "</pre>";

*/

?>
