# International Postcode System (IPCS)

**Created by Bartosz Cieślicki, 2018.**

Licencja: [Apache License 2.0](./LICENSE) — patrz też [NOTICE](./NOTICE).

## API

Publiczny endpoint do generowania i dekodowania kodów IPCS (geocode /
reverse geocode / dystans między kodami) jest udokumentowany w
[**API.md**](./API.md).

---

## 🇬🇧 Description (English)

**International Postcode System (IPCS)** is a free, open postal code
system designed for the entire world. Unlike traditional systems —
including Poland's, where a single postcode can cover an entire village
or municipality spanning several km² — IPCS **encodes geographic
coordinates directly within the code itself**.

### Why it matters

- **Precision** — every IPCS code points to an area no larger than about
  1000 m² (roughly 489 m² on average), a specific spot rather than an
  entire town.
- **No lookup database required** — because coordinates are encoded
  directly in the code string, there's no need to maintain a massive
  global database mapping codes to locations. Decoding coordinates is a
  simple mathematical operation.
- **Global coverage** — the system covers the entire surface of the
  Earth, regardless of population density, terrain, or administrative
  borders.
- **Short and readable** — 8 characters are enough to uniquely encode any
  location on Earth (for comparison: UK postcodes use up to 7 characters,
  Japanese postal codes use 7 digits).
- **Error-resistant** — the letters `O` and `I` are excluded from the
  alphabet to eliminate the most common transcription mistakes (confusion
  with `0` and `1`).

### How it works (in short)

1. Geographic coordinates (latitude and longitude) are rounded to a
   quarter of a thousandth of a degree (a grid of roughly 1000 m² at the
   equator).
2. The rounded coordinate is split into three parts: the integer degree
   part (e.g. `XXX` for longitude, 9 bits), the thousandths part (`xxx`,
   10 bits), and the rounding-quarter index (`qq`, values 00/25/50/75
   stored in 2 bits). The same applies to latitude (`YYY` — 8 bits,
   `yyy` — 10 bits, `gg` — 2 bits). These six binary numbers are then
   concatenated in a fixed order into a single 41-bit number.
3. That binary number is converted into base-34 (digits 0–9 and letters
   A–Z, excluding O and I), producing an 8-character IPCS code.
4. The reverse operation lets you reconstruct the exact center coordinates
   of the area from any IPCS code.

The full mathematical description of the algorithm, formulas, and a
reference implementation in C# are included in the project documentation.

### Example

Coordinates `-1.347120, 53.983489` → IPCS code: **`VCPM 6TKY`**

### Project status

This project is free and open to everyone — it's my contribution to the
community. Anyone can use, deploy, and build on it under the Apache 2.0
license, provided that authorship is credited (see [NOTICE](./NOTICE)).

---

## Autor / Author

**Bartosz Cieślicki** — pomysłodawca i autor algorytmu IPCS (Rochdale,
2018) / creator of the IPCS concept and algorithm (Rochdale, 2018).
---

## 🇵🇱 Opis (Polski)

**International Postcode System (IPCS)** to darmowy, otwarty system kodów
pocztowych dla całego świata. W przeciwieństwie do tradycyjnych systemów
(w tym polskiego, gdzie jeden kod pocztowy potrafi obejmować całą wieś czy
gminę o powierzchni wielu km²), IPCS **koduje współrzędne geograficzne
bezpośrednio w samym kodzie**.

### Dlaczego to ważne

- **Precyzja** — każdy kod IPCS wskazuje obszar nie większy niż ok. 1000 m²
  (średnio ok. 489 m²), czyli konkretne miejsce, a nie całą miejscowość.
- **Brak potrzeby bazy danych** — ponieważ współrzędne są zakodowane w samym
  ciągu znaków, nie trzeba utrzymywać ogromnej globalnej bazy danych
  przypisującej kody do lokalizacji. Odczyt współrzędnych to prosta operacja
  matematyczna.
- **Uniwersalność** — system pokrywa całą powierzchnię Ziemi, niezależnie od
  gęstości zaludnienia, ukształtowania terenu czy granic administracyjnych.
- **Krótki i czytelny zapis** — 8 znaków wystarcza do jednoznacznego
  zakodowania dowolnego miejsca na Ziemi (dla porównania: brytyjskie kody
  pocztowe mają do 7 znaków, japońskie 7 cyfr).
- **Odporność na pomyłki** — z alfabetu usunięto litery `O` i `I`, żeby
  wyeliminować najczęstsze błędy przy odczycie/zapisie (mylenie z `0` i `1`).

### Jak to działa (w skrócie)

1. Współrzędne geograficzne (szerokość i długość) są zaokrąglane do
   ćwiartki tysięcznej stopnia (siatka ok. 1000 m² na równiku).
2. Zaokrąglona współrzędna jest rozbijana na trzy części: część całkowitą
   stopni (np. `XXX` dla długości, 9 bitów), część tysięczną (`xxx`,
   10 bitów) oraz indeks ćwiartki zaokrąglenia (`qq`, wartości 00/25/50/75
   zapisane na 2 bitach). To samo dzieje się dla szerokości (`YYY` —
   8 bitów, `yyy` — 10 bitów, `gg` — 2 bity). Sześć tak powstałych liczb
   binarnych łączy się w ustalonej kolejności w jedną 41-bitową liczbę.
3. Liczba binarna jest konwertowana do systemu trzydziestoczwórkowego
   (0–9 i litery A–Z bez O i I) — powstaje 8-znakowy kod IPCS.
4. Operacja odwrotna pozwala z każdego kodu IPCS odtworzyć dokładne
   współrzędne środka danego obszaru.

Pełny opis matematyczny algorytmu, wzory i przykładowa implementacja w C#
znajdują się w dokumentacji projektu.

### Przykład

Współrzędne `-1.347120, 53.983489` → kod IPCS: **`VCPM 6TKY`**

### Status projektu

Projekt jest darmowy i otwarty dla wszystkich — to mój wkład dla
społeczności. Każdy może go używać, wdrażać i rozwijać na zasadach
licencji Apache 2.0, z zachowaniem informacji o autorstwie (patrz
[NOTICE](./NOTICE)).
