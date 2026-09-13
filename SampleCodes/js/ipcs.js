/*
 *	IPCS.cs
 *
 *  Created on: May 2, 2018
 *	   Project: IPCS
 *      Author: Bartosz Cieslicki
 *      E-Mail: bartc78@gmail.com
 */

	function GeneratePostcode(Lon, Lat)
	{
		if ((Lon > 180) || (Lon < -180) || (Lat > 90) || (Lat < -90)) return "";
		if (Lat > 89.99975) Lat = 89.99975;
		if (Lat < -89.99975) Lat = -89.99975;

		var Coder = [ "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F", "G", "H", "J", "K", "L", "M", "N", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z" ];

		if (Lat < 0)
		{
			Lat = (Math.ceil(Lat) + 180) + Math.abs(Lat - Math.ceil(Lat));
		}
		if (Lon < 0)
		{
			Lon = (Math.ceil(Lon) + 360) + Math.abs(Lon - Math.ceil(Lon));
		}

		var PartPostcodeLat = Lat.toFixed(6).toString();
		var PartPostcodeLon = Lon.toFixed(6).toString();

		try
		{
			var StrsLat = PartPostcodeLat.split('.');
			var StrsLon = PartPostcodeLon.split('.');

			var OptionsLat = 5;
			var Precision = Number(StrsLat[1].substring(3));
			if (Precision < 125)
			{
				OptionsLat = 0;
			}
			else if (Precision < 375)
			{
				OptionsLat = 1;
			}
			else if (Precision < 625)
			{
				OptionsLat = 2;
			}
			else if (Precision < 875)
			{
				OptionsLat = 3;
			}

			var TempHiLat = Number(StrsLat[0]);
			TempHiLat = TempHiLat * 16777216;//<< 24;
			var TempLoLat = 0;
			if (StrsLat.length > 1)
			{
				TempLoLat = Number(StrsLat[1].substring(0, 3));
				if (OptionsLat == 5)
				{
					TempLoLat++;
				}
				else
				{
					TempHiLat = TempHiLat + OptionsLat;
				}

				TempLoLat = TempLoLat * 16;//<< 4;
			}

			var OptionsLon = 5;
			Precision = Number(StrsLon[1].substring(3));
			if (Precision < 125)
			{
				OptionsLon = 0;
			}
			else if (Precision < 375)
			{
				OptionsLon = 1;
			}
			else if (Precision < 625)
			{
				OptionsLon = 2;
			}
			else if (Precision < 875)
			{
				OptionsLon = 3;
			}

			var TempHiLon = Number(StrsLon[0]);
			TempHiLon = TempHiLon * 4294967296;//<< 32;
			var TempLoLo = 0;
			if (StrsLon.length > 1)
			{
				TempLoLo = Number(StrsLon[1].substring(0, 3));
				if (OptionsLon == 5)
				{
					TempLoLo++;
				}
				else
				{
					TempHiLon = TempHiLon + (OptionsLon * 4);
				}

				TempLoLo = TempLoLo * 16384;//<< 14;
			}

			var UlongResHi = TempHiLat + TempHiLon + TempLoLat + TempLoLo;

			var Pos = 0;
			var Res = "";
			for (var i = 7; i >= 0; i--)
			{
				Pos = UlongResHi / Math.pow(34, i);
				UlongResHi -= (Math.floor(Pos) * Math.pow(34, i));
				Res += Coder[Math.floor(Pos)];
			}

			return Res;
		}
		catch(Err)
		{
			return "";
		}
	}

	function ReversePostcode(Postcode)
	{
		if (Postcode.length == 8)
		{
			Postcode = Postcode.toUpperCase();
			var Coder = "0123456789ABCDEFGHJKLMNPQRSTUVWXYZ";
			var Hi = 0;

			for (var i = 0; i < 8; i++)
			{
				if(Coder.indexOf(Postcode[i]) == -1)
				{
					return [];
				}
				Hi += Coder.indexOf(Postcode[i]) * Math.pow(34, (7 - i));
			}

			var OptionLat = (Hi & 3);
			var HalfLat = 0;
			switch (OptionLat)
			{
				case 1:
					HalfLat = 0.00025;
					break;
				case 2:
					HalfLat = 0.0005;
					break;
				case 3:
					HalfLat = 0.00075;
					break;
			}

			var OptionLon = (Hi & 12) >> 2;
			var HalfLon = 0;
			switch (OptionLon)
			{
				case 1:
					HalfLon = 0.00025;
					break;
				case 2:
					HalfLon = 0.0005;
					break;
				case 3:
					HalfLon = 0.00075;
					break;
			}

			var LoLat = (Hi >> 4) & 1023;
			var HiLat = (Hi >> 24) & 255;

			if (HiLat > 180)
			{
				Lat = -180;
				Lon = -180;
				return [];
			}
			if (HiLat > 90)
			{
				HiLat = HiLat - 180;
				Lat = HiLat - ((LoLat / 1000.0) + HalfLat);
			}
			else
			{
				Lat = HiLat + ((LoLat / 1000.0) + HalfLat);
			}

			var LoLon = (Hi >> 14) & 1023;
			var HiLon = Math.floor(Hi / 4294967296);
			
			if (HiLon > 360)
			{
				Lat = -180;
				Lon = -180;
				return [];
			}
			
			if (HiLon > 180)
			{
				HiLon = HiLon - 360;
				Lon = HiLon - ((LoLon / 1000.0) + HalfLon);
			}
			else
			{
				Lon = HiLon + ((LoLon / 1000.0) + HalfLon);
			}

			return [Lon.toFixed(5), Lat.toFixed(5)];
		}
		else
		{
			Lat = 0;
			Lon = 0;
			return [];
		}
	}
	
	function CalculateDistance(FromPostcode, ToPostcode)
	{
		var FromPC = ReversePostcode(FromPostcode);
		var ToPC = ReversePostcode(ToPostcode);
		
		if (FromPC.length > 1 && ToPC.length > 1)
		{
			var Lat1InRad = FromPC[1] * (Math.PI / 180);
			var Long1InRad = FromPC[0] * (Math.PI / 180);
			var Lat2InRad = ToPC[1] * (Math.PI / 180);
			var Long2InRad = ToPC[0] * (Math.PI / 180);
			var Longitude = Long2InRad - Long1InRad;
			var Latitude = Lat2InRad - Lat1InRad;
			var a = Math.pow(Math.sin(Latitude / 2.0), 2.0) + Math.cos(Lat1InRad) * Math.cos(Lat2InRad) * Math.pow(Math.sin(Longitude / 2.0), 2.0);
			var c = 2.0 * Math.atan2(Math.sqrt(a), Math.sqrt(1.0 - a));
			var EarthRadiusKms = 6376.5;
			return (EarthRadiusKms * c);
		}
		else
		{
			return -1;
		}
	}