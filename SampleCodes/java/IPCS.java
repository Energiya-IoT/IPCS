/*
 *	IPCS.java
 *
 *  Created on: May 2, 2018
 *	   Project: IPCS
 *      Author: Bartosz Cieslicki
 *      E-Mail: bartc78@gmail.com
 */

public class IPCS
{
    public static String GeneratePostcode(double Lon, double Lat)
    {
        if ((Lon > 180) || (Lon < -180) || (Lat > 90) || (Lat < -90)) return "";
        if (Lat > 89.99975) Lat = 89.99975;
        if (Lat < -89.99975) Lat = -89.99975;

        String[] Coder = { "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F", "G", "H", "J", "K", "L", "M", "N", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z" };

        if (Lat < 0)
        {
            Lat = ((long)Lat + 180) + Math.abs(Lat - (long)Lat);
        }
        if (Lon < 0)
        {
            Lon = ((long)Lon + 360) + Math.abs(Lon - (long)Lon);
        }

        String PartPostcodeLat = String.format ("%.6f", Lat).replace(',','.');
        String PartPostcodeLon = String.format ("%.6f", Lon).replace(',','.');

        try
        {
            String[] StrsLat = PartPostcodeLat.split("\\.");
            String[] StrsLon = PartPostcodeLon.split("\\.");

            long OptionsLat = 5;
            long Precision =Integer.valueOf(StrsLat[1].substring(3));
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

            long TempHiLat = Long.valueOf(StrsLat[0]);
            TempHiLat = TempHiLat << 24;
            long TempLoLat = 0;
            if (StrsLat.length > 1)
            {
                TempLoLat = Long.valueOf(StrsLat[1].substring(0, 3));
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

            long OptionsLon = 5;
            Precision = Integer.valueOf(StrsLon[1].substring(3));
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

            long TempHiLon = Long.valueOf(StrsLon[0]);
            TempHiLon = TempHiLon << 32;
            long TempLoLo = 0;
            if (StrsLon.length > 1)
            {
                TempLoLo = Long.valueOf(StrsLon[1].substring(0, 3));
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

            long UlongResHi = TempHiLat | TempHiLon | TempLoLat | TempLoLo;

            long Pos = 0;
            String Res = "";
            for (int i = 7; i >= 0; i--)
            {
                Pos = UlongResHi / (long)Math.pow(34, i);
                UlongResHi -= (Pos * (long)Math.pow(34, i));
                Res += Coder[(int)Pos];
            }

            return Res;
        }
        catch(Exception e)
        {
            return "";
        }
    }

    public static LatLong ReversePostcode(String Postcode)
    {
        double Lat;
        double Lon;
        if (Postcode.length() == 8)
        {
            Postcode = Postcode.toUpperCase();
            String Coder = "0123456789ABCDEFGHJKLMNPQRSTUVWXYZ";
            long Hi = 0;

            for (int i = 0; i < 8; i++)
            {
                Hi += (long)Coder.indexOf(Postcode.charAt(i)) * (long)Math.pow(34, (7 - i));
            }

            long OptionLat = (Hi & 3);
            double HalfLat = 0;
            switch ((int)OptionLat)
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

            long OptionLon = (Hi & 12) >> 2;
            double HalfLon = 0;
            switch ((int)OptionLon)
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
                return null;
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
                return null;
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

            return new LatLong(Lon, Lat);
        }
        else
        {
            return null;
        }
    }
	
	public static double CalculateDistance(String FromPostcode, String ToPostcode)
    {
        LatLong FromPC = ReversePostcode(FromPostcode);
        LatLong ToPC = ReversePostcode(ToPostcode);

        if ((FromPC != null) && (ToPC != null))
        {
            double Lat1InRad = FromPC.getLat() * (Math.PI / 180.0);
            double Long1InRad = FromPC.getLon() * (Math.PI / 180.0);
            double Lat2InRad = ToPC.getLat() * (Math.PI / 180.0);
            double Long2InRad = ToPC.getLon() * (Math.PI / 180.0);
            double Longitude = Long2InRad - Long1InRad;
            double Latitude = Lat2InRad - Lat1InRad;
            double a = Math.pow(Math.sin(Latitude / 2.0), 2.0) + Math.cos(Lat1InRad) * Math.cos(Lat2InRad) * Math.pow(Math.sin(Longitude / 2.0), 2.0);
            double c = 2.0 * Math.atan2(Math.sqrt(a), Math.sqrt(1.0 - a));
            double EarthRadiusKms = 6376.5;
            return (EarthRadiusKms * c);
        }
        else
        {
            return -1;
        }
    }

    public static class LatLong
    {
        private double Lat;
        private double Lon;

        private LatLong(double Lon, double Lat)
        {
            this.Lat = Lat;
            this.Lon = Lon;
        }

        public double getLat()
        {
            return Lat;
        }

        public double getLon()
        {
            return Lon;
        }
    }
}
