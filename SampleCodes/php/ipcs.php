<?php 

    function GeneratePostcode($Lon, $Lat)
	{
		if (($Lon > 180) || ($Lon < -180) || ($Lat > 90) || ($Lat < -90)) return "";
		if ($Lat > 89.99975) $Lat = 89.99975;
		if ($Lat < -89.99975) $Lat = -89.99975;

		$Coder = [ "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F", "G", "H", "J", "K", "L", "M", "N", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z" ];

		if ($Lat < 0)
		{
			$Lat = (ceil($Lat) + 180) + abs($Lat - ceil($Lat));
		}
		if ($Lon < 0)
		{
			$Lon = (ceil($Lon) + 360) + abs($Lon - ceil($Lon));
		}

		$PartPostcodeLat = number_format(round($Lat, 6), 6, ".", "");
		$PartPostcodeLon = number_format(round($Lon, 6), 6, ".", "");

		try
		{
			$StrsLat = explode(".", $PartPostcodeLat);
			$StrsLon = explode(".", $PartPostcodeLon);

			$OptionsLat = 5;
			$Precision = intval(substr($StrsLat[1], 3));
			if ($Precision < 125)
			{
				$OptionsLat = 0;
			}
			else if ($Precision < 375)
			{
				$OptionsLat = 1;
			}
			else if ($Precision < 625)
			{
				$OptionsLat = 2;
			}
			else if ($Precision < 875)
			{
				$OptionsLat = 3;
			}

			$TempHiLat = intval($StrsLat[0]);
			$TempHiLat = $TempHiLat * 16777216;//<< 24;
			$TempLoLat = 0;
			if (count($StrsLat) > 1)
			{
				$TempLoLat = intval(substr($StrsLat[1], 0, 3));
				if ($OptionsLat == 5)
				{
					$TempLoLat++;
				}
				else
				{
					$TempHiLat = $TempHiLat + $OptionsLat;
				}

				$TempLoLat = $TempLoLat * 16;//<< 4;
			}

			$OptionsLon = 5;
			$Precision = intval(substr($StrsLon[1], 3));			
			if ($Precision < 125)
			{
				$OptionsLon = 0;
			}
			else if ($Precision < 375)
			{
				$OptionsLon = 1;
			}
			else if ($Precision < 625)
			{
				$OptionsLon = 2;
			}
			else if ($Precision < 875)
			{
				$OptionsLon = 3;
			}

			$TempHiLon = intval($StrsLon[0]);
			$TempHiLon = $TempHiLon * 4294967296;//<< 32;
			
			$TempLoLo = 0;
			if (count($StrsLon) > 1)
			{
				$TempLoLo = intval(substr($StrsLon[1], 0, 3));
				if ($OptionsLon == 5)
				{
					$TempLoLo++;
				}
				else
				{
					$TempHiLon = $TempHiLon + ($OptionsLon * 4);
				}

				$TempLoLo = $TempLoLo * 16384;//<< 14;
			}

			$UlongResHi = $TempHiLat + $TempHiLon + $TempLoLat + $TempLoLo;
	   
			$Pos = 0;
			$Res = "";
			for ($i = 7; $i >= 0; $i--)
			{
				$Pos = $UlongResHi / pow(34, $i);
				$UlongResHi -= (floor($Pos) * pow(34, $i));
				$Res .= $Coder[floor($Pos)];
			}

			return $Res;
		}
		catch(Exception $Err)
		{
			return "";
		}
	}

	function ReversePostcode($Postcode)
	{
		if (strlen($Postcode) == 8)
		{
			$Postcode = strtoupper($Postcode);
			$Coder = "0123456789ABCDEFGHJKLMNPQRSTUVWXYZ";
			$Hi = 0;

			for ($i = 0; $i < 8; $i++)
			{
				$ResPos = strpos($Coder, $Postcode[$i]);
				if($ResPos === FALSE)
				{
					return [];
				}
				$Hi += $ResPos * pow(34, (7 - $i));
			}

			$OptionLat = ($Hi & 3);
			$HalfLat = 0;
			switch ($OptionLat)
			{
				case 1:
					$HalfLat = 0.00025;
					break;
				case 2:
					$HalfLat = 0.0005;
					break;
				case 3:
					$HalfLat = 0.00075;
					break;
			}

			$OptionLon = ($Hi & 12) >> 2;
			$HalfLon = 0;
			switch ($OptionLon)
			{
				case 1:
					$HalfLon = 0.00025;
					break;
				case 2:
					$HalfLon = 0.0005;
					break;
				case 3:
					$HalfLon = 0.00075;
					break;
			}

			$LoLat = ($Hi >> 4) & 1023;
			$HiLat = ($Hi >> 24) & 255;

			if ($HiLat > 180)
			{
				$Lat = -180;
				$Lon = -180;
				return [];
			}
			if ($HiLat > 90)
			{
				$HiLat = $HiLat - 180;
				$Lat = $HiLat - (($LoLat / 1000.0) + $HalfLat);
			}
			else
			{
				$Lat = $HiLat + (($LoLat / 1000.0) + $HalfLat);
			}

			$LoLon = ($Hi >> 14) & 1023;
			$HiLon = floor($Hi / 4294967296);
			
			if ($HiLon > 360)
			{
				$Lat = -180;
				$Lon = -180;
				return [];
			}
			
			if ($HiLon > 180)
			{
				$HiLon = $HiLon - 360;
				$Lon = $HiLon - (($LoLon / 1000.0) + $HalfLon);
			}
			else
			{
				$Lon = $HiLon + (($LoLon / 1000.0) + $HalfLon);
			}

			return [round($Lon, 5), round($Lat, 5)];
		}
		else
		{
			$Lat = 0;
			$Lon = 0;
			return [];
		}
	}
	
	function CalculateDistance($FromPostcode, $ToPostcode)
	{
		$FromPC = ReversePostcode($FromPostcode);
		$ToPC = ReversePostcode($ToPostcode);
		if (count($FromPC) > 1 && count($ToPC) > 1)
		{
			$Lat1InRad = $FromPC[1] * (M_PI / 180.0);
			$Long1InRad = $FromPC[0] * (M_PI / 180.0);
			$Lat2InRad = $ToPC[1] * (M_PI / 180.0);
			$Long2InRad = $ToPC[0] * (M_PI / 180.0);
			$Longitude = $Long2InRad - $Long1InRad;
			$Latitude = $Lat2InRad - $Lat1InRad;
			$a = pow(sin($Latitude / 2.0), 2.0) + cos($Lat1InRad) * cos($Lat2InRad) * pow(sin($Longitude / 2.0), 2.0);
			$c = 2.0 * atan2(sqrt($a), sqrt(1.0 - $a));
			$EarthRadiusKms = 6376.5;
			return ($EarthRadiusKms * $c);
		}
		else
		{
			return -1;
		}
	}
?>