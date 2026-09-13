# IPCS WebService — API Documentation

WebService has got six functions, which allow you to get addresses from a
specified IPCS, find an IPCS based on an address, insert an address into
the database, convert coordinates to and from IPCS, and calculate the
distance between two IPCS codes.

**WebService location:**

```
http://<domain>/WebService/
```

All responses are JSON. Every response includes a `"success"` boolean;
on failure it's typically accompanied by an `"error"` message.

---

## Table of contents

- [Get addresses from IPCS](#get-addresses-from-ipcs)
- [Find IPCS based on address](#find-ipcs-based-on-address)
- [Insert address](#insert-address)
- [Generate IPCS from coordinates (geocode)](#generate-ipcs-from-coordinates-geocode)
- [Get coordinates from IPCS (reverse geocode)](#get-coordinates-from-ipcs-reverse-geocode)
- [Calculate distance between two IPCS codes](#calculate-distance-between-two-ipcs-codes)
- [Navigation](#navigation)

---

## Get addresses from IPCS

```
http://<domain>/WebService/indexAPI.php?action=getaddresses&ipcs=<IPCS>
```

### Parameters

| Parameter | Type   | Example    | Description          |
|-----------|--------|------------|-----------------------|
| action    | String | getaddresses | Constant value.     |
| ipcs      | String | V9X0KFPC   | Full or part of IPCS. |

### Response

```json
{
  "success": true,
  "addresses": [{
    "address": "64 Newbold Hall Drive",
    "town": "Rochdale",
    "country": "United Kingdom",
    "poi": "Hospital",
    "ipcs": "V9X0KFPC"
  }]
}
```

---

## Find IPCS based on address

```
http://<domain>/WebService/indexAPI.php?action=getpostcodes&address=<address>&town=<town>&ipcs=<postcode>&poi=<poi>&country=<count>
```

### Parameters

| Parameter | Type   | Example      | Description                          |
|-----------|--------|--------------|----------------------------------------|
| action    | String | getpostcodes | Constant value.                      |
| address   | String | Newbold      | Full or part of address (optional).  |
| town      | String | Rochdale     | Full or part of town (optional).     |
| ipcs      | String | V9X0         | Full or part of ipcs (optional).     |
| poi       | String | Hosp         | Full or part of point of interest (optional). |
| country   | String | United       | Full or part of country (optional). |

### Response

```json
{
  "success": true,
  "ipcs": ["V9X0KFPC", "V9X0KFPB"]
}
```

---

## Insert address

Address will be visible in the system after verification.

```
http://<domain>/WebService/indexAPI.php?action=insertaddress&address=<address>&town=<town>&poi=<poi>&ipcs=<postcode>&country=<count>
```

### Parameters

| Parameter | Type   | Example       | Description       |
|-----------|--------|---------------|--------------------|
| action    | String | insertaddress | Constant value.   |
| address   | String | Koszykowa 1   | Full address.     |
| town      | String | Warszawa      | Town or city.     |
| poi       | String | Hospital      | Point of interest.|
| ipcs      | String | 1QYAVDSH      | IPCS.             |
| country   | String | Poland        | Country in english. |

### Response

```json
{
  "success": true
}
```

---

## Generate IPCS from coordinates (geocode)

Converts a pair of geographic coordinates (longitude, latitude) into an
8-character International Postcode.

```
http://<domain>/WebService/indexAPI.php?action=geocode&lon=<longitude>&lat=<latitude>
```

### Parameters

| Parameter | Type   | Example  | Description                             |
|-----------|--------|----------|-------------------------------------------|
| action    | String | geocode  | Constant value.                          |
| lon       | Number | -2.0002  | Longitude, decimal degrees (-180 to 180).|
| lat       | Number | 54.3222  | Latitude, decimal degrees (-90 to 90).   |

### Example

```
http://<domain>/WebService/indexAPI.php?action=geocode&lon=-2.0002&lat=54.3222
```

```json
{
  "success": true,
  "Postcode": "1GL8YT7F"
}
```

If the supplied coordinates are outside the valid range, or the request
cannot be processed:

```json
{
  "success": false,
  "error": "<error message>"
}
```

---

## Get coordinates from IPCS (reverse geocode)

Converts a full 8-character International Postcode back into the
coordinates of the centre of its sector.

```
http://<domain>/WebService/indexAPI.php?action=reversegeocode&ipcs=<IPCS>
```

### Parameters

| Parameter | Type   | Example  | Description           |
|-----------|--------|----------|--------------------------|
| action    | String | reversegeocode | Constant value.  |
| ipcs      | String | 1GL8YT7F | Full 8-character IPCS.  |

### Example

```
http://<domain>/WebService/indexAPI.php?action=reversegeocode&ipcs=1GL8YT7F
```

```json
{
  "success": true,
  "Lon": 18.7715,
  "Lat": 50.31025
}
```

If the IPCS is not exactly 8 characters long, or contains invalid
characters:

```json
{
  "success": false,
  "error": "Wrong postcode."
}
```

---

## Calculate distance between two IPCS codes

Calculates the great-circle distance, in kilometres, between the centres
of the sectors identified by two IPCS codes.

```
http://<domain>/WebService/indexAPI.php?action=distance&from=<IPCS>&to=<IPCS>
```

### Parameters

| Parameter | Type   | Example  | Description                              |
|-----------|--------|----------|---------------------------------------------|
| action    | String | distance | Constant value.                            |
| from      | String | 1GL8YT7F | Full 8-character IPCS of the starting point. |
| to        | String | V9X0KFPC | Full 8-character IPCS of the destination.  |

### Example

```
http://<domain>/WebService/indexAPI.php?action=distance&from=1GL8YT7F&to=V9X0KFPC
```

```json
{
  "success": true,
  "Distance": 245.187
}
```

If either IPCS code is invalid:

```json
{
  "success": false,
  "error": "Distance cannot be calculated."
}
```

---

## Navigation

```
http://<domain>?ipcs=<ipcs>
http://<domain>?coord=<latitude>,<longitude>
http://<domain>?lat=<latitude>&lng=<longitude>
```

### Examples

```
http://<domain>?ipcs=062WT853
http://<domain>?coord=53.344,0.22333
http://<domain>?lat=54.3222&lng=-2.0002
```
