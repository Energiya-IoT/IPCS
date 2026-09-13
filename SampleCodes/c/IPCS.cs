using System;

/*
 *	IPCS.cs
 *
 *  Created on: May 2, 2018
 *	   Project: IPCS
 *      Author: Bartosz Cieslicki
 *      E-Mail: bartc78@gmail.com
 */

namespace IPCS
{
    class IPCS
    {
        public static string GeneratePostcode(double Lon, double Lat)
        {
            if ((Lon > 180) || (Lon < -180) || (Lat > 90) || (Lat < -90)) return "";
            if (Lat > 89.99975) Lat = 89.99975;
            if (Lat < -89.99975) Lat = -89.99975;

            string[] Coder = { "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F", "G", "H", "J", "K", "L", "M", "N", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z" };

            if (Lat < 0)
            {
                Lat = ((long)Lat + 180) + Math.Abs(Lat - (long)Lat);
            }
            if (Lon < 0)
            {
                Lon = ((long)Lon + 360) + Math.Abs(Lon - (long)Lon);
            }

            string PartPostcodeLat = Lat.ToString("000.000000");
            string PartPostcodeLon = Lon.ToString("000.000000");

            try
            {
                string[] StrsLat = PartPostcodeLat.Split('.');
                string[] StrsLon = PartPostcodeLon.Split('.');

                ulong OptionsLat = 5;
                long Precision = Convert.ToInt32(StrsLat[1].Substring(3));
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

                ulong TempHiLat = Convert.ToUInt64(StrsLat[0]);
                TempHiLat = TempHiLat << 24;
                ulong TempLoLat = 0;
                if (StrsLat.Length > 1)
                {
                    TempLoLat = Convert.ToUInt64(StrsLat[1].Substring(0, 3));
                    if (OptionsLat == 5)
                    {
                        TempLoLat++;
                    }
                    else
                    {
                        TempHiLat = TempHiLat | OptionsLat;
                    }

                    TempLoLat = TempLoLat << 4;
                }

                ulong OptionsLon = 5;
                Precision = Convert.ToInt32(StrsLon[1].Substring(3));
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

                ulong TempHiLon = Convert.ToUInt64(StrsLon[0]);
                TempHiLon = TempHiLon << 32;
                ulong TempLoLo = 0;
                if (StrsLon.Length > 1)
                {
                    TempLoLo = Convert.ToUInt64(StrsLon[1].Substring(0, 3));
                    if (OptionsLon == 5)
                    {
                        TempLoLo++;
                    }
                    else
                    {
                        TempHiLon = TempHiLon | (OptionsLon * 4);
                    }

                    TempLoLo = TempLoLo << 14;
                }

                ulong UlongResHi = TempHiLat | TempHiLon | TempLoLat | TempLoLo;

                ulong Pos = 0;
                string Res = "";
                for (int i = 7; i >= 0; i--)
                {
                    Pos = UlongResHi / (ulong)Math.Pow(34, i);
                    UlongResHi -= (Pos * (ulong)Math.Pow(34, i));
                    Res += Coder[Pos];
                }

                return Res;
            }
            catch
            {
                return "";
            }
        }

        public static bool ReversePostcode(string Postcode, out double Lon, out double Lat)
        {
            if (Postcode.Length == 8)
            {
                Postcode = Postcode.ToUpper();
                string Coder = "0123456789ABCDEFGHJKLMNPQRSTUVWXYZ";
                ulong Hi = 0;

                for (int i = 0; i < 8; i++)
                {
                    Hi += (ulong)Coder.IndexOf(Postcode[i]) * (ulong)Math.Pow(34, (7 - i));
                }

                ulong OptionLat = (Hi & 3);
                double HalfLat = 0;
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

                ulong OptionLon = (Hi & 12) >> 2;
                double HalfLon = 0;
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

                long LoLat = ((long)Hi >> 4) & 1023;
                long HiLat = ((long)Hi >> 24) & 255;
                if (HiLat > 180)
                {
                    Lat = -180;
                    Lon = -180;
                    return false;
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

                long LoLon = ((long)Hi >> 14) & 1023;
                long HiLon = ((long)Hi >> 32);
                if (HiLon > 360)
                {
                    Lat = -180;
                    Lon = -180;
                    return false;
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

                return true;
            }
            else
            {
                Lat = 0;
                Lon = 0;
                return false;
            }
        }

        public static double CalculateDistance(string FromPostcode, string ToPostcode)
        {
            double x1;
            double y1;
            double x2;
            double y2;

            if (ReversePostcode(FromPostcode, out x1, out y1) && ReversePostcode(ToPostcode, out x2, out y2))
            {
                double Lat1InRad = (y1) * (Math.PI / 180);
                double Long1InRad = (x1) * (Math.PI / 180);
                double Lat2InRad = (y2) * (Math.PI / 180);
                double Long2InRad = (x2) * (Math.PI / 180);
                double Longitude = Long2InRad - Long1InRad;
                double Latitude = Lat2InRad - Lat1InRad;
                double a = Math.Pow(Math.Sin(Latitude / 2.0), 2.0) + Math.Cos(Lat1InRad) * Math.Cos(Lat2InRad) * Math.Pow(Math.Sin(Longitude / 2.0), 2.0);
                double c = 2.0 * Math.Atan2(Math.Sqrt(a), Math.Sqrt(1.0 - a));
                double EarthRadiusKms = 6376.5;
                return (EarthRadiusKms * c);
            }
            else
            {
                return -1;
            }
        }
    }
}
