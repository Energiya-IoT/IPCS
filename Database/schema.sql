--
-- IPCS database schema (structure only, no data)
-- PostgreSQL
--
-- Generated for the public IPCS repository. To regenerate this file
-- yourself from a live database, use:
--
--   pg_dump --schema-only --no-owner --no-privileges -d IPCS > schema.sql
--

SET client_encoding = 'UTF8';
SET standard_conforming_strings = 'on';

-- Uncomment if you want this script to create the database itself.
-- Requires connecting as a superuser first (e.g. via `psql -U postgres`).
--
-- CREATE DATABASE "IPCS" WITH TEMPLATE = template0 ENCODING = 'UTF8'
--     LOCALE_PROVIDER = libc LOCALE = 'en_US.UTF-8';

-- ---------------------------------------------------------------------
-- Sequences
-- ---------------------------------------------------------------------

CREATE SEQUENCE public.addresses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE SEQUENCE public.visit_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

-- ---------------------------------------------------------------------
-- Tables
-- ---------------------------------------------------------------------

CREATE TABLE public.addresses (
    id bigint DEFAULT nextval('public.addresses_id_seq'::regclass) NOT NULL,
    address text,
    town text,
    country text,
    postcode text,
    confirmed boolean,
    poi text
);

CREATE TABLE public.visits (
    id bigint DEFAULT nextval('public.visit_id_seq'::regclass) NOT NULL,
    ip character varying(20)
);

-- ---------------------------------------------------------------------
-- Constraints
-- ---------------------------------------------------------------------

ALTER TABLE ONLY public.addresses
    ADD CONSTRAINT addresses_pk PRIMARY KEY (id);

ALTER TABLE ONLY public.visits
    ADD CONSTRAINT newtable_pk PRIMARY KEY (id);

-- ---------------------------------------------------------------------
-- Indexes
-- ---------------------------------------------------------------------

CREATE INDEX addresses_id_idx ON public.addresses USING btree (id);

-- ---------------------------------------------------------------------
-- Functions
-- ---------------------------------------------------------------------

-- Returns confirmed addresses matching a postcode prefix.
CREATE FUNCTION public."GetAddresses"(searchpostcode character varying)
RETURNS TABLE(addr text, city text, ctry text, pc text, poii text, conf boolean)
    LANGUAGE plpgsql
    AS $$
begin
 return query SELECT address, town, country, postcode, poi, confirmed
FROM public.addresses
  WHERE (postcode ilike searchpostcode || '%') and (confirmed='true');
end;
$$;

-- Returns confirmed postcodes matching partial address/town/country/poi/postcode filters.
CREATE FUNCTION public."GetPostcodes"(
    addr character varying,
    city character varying,
    ctry character varying,
    poii character varying,
    searchpc character varying
)
RETURNS TABLE(pcode text)
    LANGUAGE plpgsql
    AS $$
begin
 return query SELECT postcode
FROM public.addresses
  WHERE ((postcode ilike searchpc || '%') and (town ilike city || '%') and (country ilike ctry || '%') and (address ilike '%' || addr || '%') and (poi ilike '%' || poii || '%')) and (confirmed='true');
end;
$$;

-- Inserts a new, unconfirmed address (awaiting moderation).
CREATE FUNCTION public."InsertAddress"(
    address character varying,
    town character varying,
    country character varying,
    poi character varying,
    postcode character varying
)
RETURNS boolean
    LANGUAGE plpgsql
    AS $$
begin
	begin
        INSERT INTO public."addresses"
        ("address", "town", "country", "postcode", "poi", "confirmed")
        VALUES(address, town, country, postcode, poi, 'false');
        return true;
    end;
end;
$$;
