-- $Id: func.sql,v 1.2 2004/04/16 22:25:38 mmr Exp $

--/-/-/-/-/---------------------
-- Functions

--
-- func_b1nRandom: Returns a random int between min and max (inclusive)
--
CREATE OR REPLACE FUNCTION func_b1nRandom(int, int) RETURNS INT AS '
DECLARE
  min ALIAS FOR $1;
  max ALIAS FOR $2;
BEGIN
  RETURN min + (RANDOM()*(max - min))::int;
END;'
LANGUAGE 'plpgsql';

--
-- Overloading of b1nRandom but with min assumed as 0
--
CREATE OR REPLACE FUNCTION func_b1nRandom(int) RETURNS INT AS '
DECLARE
  max ALIAS FOR $1;
BEGIN
  RETURN func_b1nRandom(0, max);
END;'
LANGUAGE 'plpgsql';

--
-- func_newPlayer: Creates a new player
--
CREATE FUNCTION func_newPlayer(text, text, text, int) RETURNS BOOLEAN AS '
DECLARE
  -- Args
  var_login   ALIAS FOR $1;
  var_passwd  ALIAS FOR $2;
  var_email   ALIAS FOR $3;
  var_master_id  ALIAS FOR $4;

  -- Aux
  r record;
BEGIN
  -- Getting a random position in a random City
  SELECT INTO r
    cit_id,
    func_b1nRandom(cit_pos_x0, cit_pos_x1) AS pos_x,
    func_b1nRandom(cit_pos_y0, cit_pos_y1) AS pos_y
  FROM
    city
  ORDER BY
    RANDOM()
  LIMIT 1;

  -- Creating player
  INSERT INTO player (
    pla_parent_id,
    cit_id,
    pla_login,
    pla_passwd,
    pla_email,
    pla_pos_x,
    pla_pos_y
  )
  VALUES (
    var_master_id,
    r.cit_id,
    var_login,
    var_passwd,
    var_email,
    r.pos_x,
    r.pos_y
  );
  RETURN ''t'';
END;
' LANGUAGE 'plpgsql';

--
--  func_mapGetBuildings: Get buildings around
--
--  Usage:
--  SELECT
--    *
--  FROM
--    func_mapGetBuildings(city, pos_x, pos_y) AS (
--      bty_name  text,
--      bty_code  text,
--      bui_id    int,
--      bui_code  text,
--      bui_hold  int,
--      bui_pos_x int,
--      bui_pos_y int
--    );
CREATE FUNCTION func_mapGetBuildings(int, int, int) RETURNS SETOF RECORD AS '
DECLARE
  -- Args
  var_cit ALIAS FOR $1;
  var_x   ALIAS FOR $2;
  var_y   ALIAS FOR $3;

  -- Control Vars
  var_ret   RECORD;
  var_query TEXT;
BEGIN
  var_query := ''
    SELECT
      bty_name,
      bty_code,
      bui_id,
      bui_code,
      bui_hold,
      bui_pos_x,
      bui_pos_y
    FROM
      view_building
    WHERE
      cit_id      = '' || (var_cit) || '' AND
      ((bui_pos_x = '' || (var_x-1) || '' AND bui_pos_y = '' || (var_y-1) || '') OR
       (bui_pos_x = '' || (var_x)   || '' AND bui_pos_y = '' || (var_y-1) || '') OR
       (bui_pos_x = '' || (var_x+1) || '' AND bui_pos_y = '' || (var_y-1) || '') OR

       (bui_pos_x = '' || (var_x-1) || '' AND bui_pos_y = '' || (var_y)   || '') OR
       (bui_pos_x = '' || (var_x)   || '' AND bui_pos_y = '' || (var_y)   || '') OR
       (bui_pos_x = '' || (var_x+1) || '' AND bui_pos_y = '' || (var_y)   || '') OR

       (bui_pos_x = '' || (var_x-1) || '' AND bui_pos_y = '' || (var_y+1) || '') OR
       (bui_pos_x = '' || (var_x)   || '' AND bui_pos_y = '' || (var_y+1) || '') OR
       (bui_pos_x = '' || (var_x+1) || '' AND bui_pos_y = '' || (var_y+1) || ''))
    ORDER BY
      bui_pos_x, bui_pos_y'';

  -- RAISE NOTICE ''Query: %'', var_query;

  FOR var_ret IN EXECUTE var_query LOOP
    RETURN NEXT var_ret;
  END LOOP; 
  RETURN var_ret;
END;
' LANGUAGE 'plpgsql';

--
--  func_mapGetPlayers: Get active players around
--
--  Usage:
-- 
--  SELECT
--    *
--  FROM
--    func_mapGetPlayers(city, pos_x, pos_y, pla_id, days_expiration) AS (
--      pla_id    int,
--      pla_login text,
--      pla_exp   int,
--      pla_pos_x int,
--      pla_pos_y int
--    );
CREATE FUNCTION func_mapGetPlayers(int, int, int, int, interval) RETURNS SETOF RECORD AS '
DECLARE
  -- Args
  var_cit   ALIAS FOR $1;
  var_x     ALIAS FOR $2;
  var_y     ALIAS FOR $3;
  var_myid  ALIAS FOR $4;
  var_days  ALIAS FOR $5;

  -- Control Vars
  var_ret   RECORD;
  var_query TEXT; 
BEGIN
  var_query := ''
    SELECT
      pla_id,
      pla_login,
      pla_exp,
      pla_pos_x,
      pla_pos_y
    FROM
      player
    WHERE
      pla_id != '' || var_myid || '' AND
      pla_active = true AND 
      pla_last_login > 
        (CURRENT_TIMESTAMP::timestamp - '''''' || var_days || ''''''::interval) AND
      cit_id      = '' || (var_cit) || '' AND
      ((pla_pos_x = '' || (var_x-1) || '' AND pla_pos_y = '' || (var_y-1) || '') OR
       (pla_pos_x = '' || (var_x)   || '' AND pla_pos_y = '' || (var_y-1) || '') OR
       (pla_pos_x = '' || (var_x+1) || '' AND pla_pos_y = '' || (var_y-1) || '') OR

       (pla_pos_x = '' || (var_x-1) || '' AND pla_pos_y = '' || (var_y)   || '') OR
       (pla_pos_x = '' || (var_x)   || '' AND pla_pos_y = '' || (var_y)   || '') OR
       (pla_pos_x = '' || (var_x+1) || '' AND pla_pos_y = '' || (var_y)   || '') OR

       (pla_pos_x = '' || (var_x-1) || '' AND pla_pos_y = '' || (var_y+1) || '') OR
       (pla_pos_x = '' || (var_x)   || '' AND pla_pos_y = '' || (var_y+1) || '') OR
       (pla_pos_x = '' || (var_x+1) || '' AND pla_pos_y = '' || (var_y+1) || ''))
    ORDER BY
      pla_pos_x, pla_pos_y, pla_exp'';

  -- RAISE NOTICE ''Query: %'', var_query;

  FOR var_ret IN EXECUTE var_query LOOP
    RETURN NEXT var_ret;
  END LOOP; 
  RETURN var_ret;
END;
' LANGUAGE 'plpgsql';

--
--  func_mapGetNPCs: Get NPCs around
--
--  Usage:
-- 
--  SELECT
--    *
--  FROM
--    func_mapGetNPCs(city, pos_x, pos_y) AS (
--      nty_name  text,
--      nty_code  text,
--      nty_blood int,
--      npc_id    int,
--      npc_cname text,
--      npc_pos_x int,
--      npc_pos_y int
--    );
CREATE FUNCTION func_mapGetNPCs(int, int, int) RETURNS SETOF RECORD AS '
DECLARE
  -- Args
  var_cit ALIAS FOR $1;
  var_x   ALIAS FOR $2;
  var_y   ALIAS FOR $3;

  -- Control Vars
  var_ret   RECORD;
  var_query TEXT; 
BEGIN
  var_query := ''
    SELECT
      nty_name,
      nty_code,
      nty_blood,
      npc_id,
      npc_pos_x,
      npc_pos_y
    FROM
      view_npc
    WHERE
      cit_id      = '' || (var_cit) || '' AND
      ((npc_pos_x = '' || (var_x-1) || '' AND npc_pos_y = '' || (var_y-1) || '') OR
       (npc_pos_x = '' || (var_x)   || '' AND npc_pos_y = '' || (var_y-1) || '') OR
       (npc_pos_x = '' || (var_x+1) || '' AND npc_pos_y = '' || (var_y-1) || '') OR

       (npc_pos_x = '' || (var_x-1) || '' AND npc_pos_y = '' || (var_y)   || '') OR
       (npc_pos_x = '' || (var_x)   || '' AND npc_pos_y = '' || (var_y)   || '') OR
       (npc_pos_x = '' || (var_x+1) || '' AND npc_pos_y = '' || (var_y)   || '') OR

       (npc_pos_x = '' || (var_x-1) || '' AND npc_pos_y = '' || (var_y+1) || '') OR
       (npc_pos_x = '' || (var_x)   || '' AND npc_pos_y = '' || (var_y+1) || '') OR
       (npc_pos_x = '' || (var_x+1) || '' AND npc_pos_y = '' || (var_y+1) || ''))
    ORDER BY
      npc_pos_x, npc_pos_y'';

  -- RAISE NOTICE ''Query: %'', var_query;

  FOR var_ret IN EXECUTE var_query LOOP
    RETURN NEXT var_ret;
  END LOOP; 
  RETURN var_ret;
END;
' LANGUAGE 'plpgsql';

--
--  func_mapGetStreetJunctions: Get street junctions around
--
--  Usage:
-- 
--  SELECT
--    *
--  FROM
--    func_mapGetStreetJunctions(city, pos_x, pos_y) AS (
--      bty_name  text,
--      bty_code  text,
--      bui_id    int,
--      bui_code  text,
--      bui_hold  int,
--      bui_pos_x int,
--      bui_pos_y int
--    );
CREATE FUNCTION func_mapGetStreetJunctions(int, int, int) RETURNS SETOF RECORD AS '
DECLARE
  -- Args
  var_cit ALIAS FOR $1;
  var_x   ALIAS FOR $2;
  var_y   ALIAS FOR $3;

  -- Control Vars
  var_ret   RECORD;
  var_query TEXT; 
BEGIN
  var_query := ''
    SELECT
      s1.str_code   AS str1_code,
      s2.str_code   AS str2_code,
      s1.str_pos_x1 AS str_pos_x,
      s2.str_pos_y1 AS str_pos_y
    FROM
      street s1,
      street s2
    WHERE
      s1.cit_id       = '' || (var_cit) || '' AND
      s2.cit_id       = '' || (var_cit) || '' AND 

      ((s1.str_pos_x1 = '' || (var_x)   || '' AND s2.str_pos_y0 = '' || (var_y)   || '')  OR

      ((s1.str_pos_x1 = '' || (var_x-1) || '' AND s2.str_pos_y0 = '' || (var_y)   || '')  OR 
       (s1.str_pos_x1 = '' || (var_x+1) || '' AND s2.str_pos_y0 = '' || (var_y)   || '')) OR

      ((s1.str_pos_x1 = '' || (var_x)   || '' AND s2.str_pos_y0 = '' || (var_y-1) || '')  OR 
       (s1.str_pos_x1 = '' || (var_x)   || '' AND s2.str_pos_y0 = '' || (var_y+1) || '')) OR

      ((s1.str_pos_x1 = '' || (var_x-1) || '' AND s2.str_pos_y1 = '' || (var_y-1) || '')  OR
       (s1.str_pos_x1 = '' || (var_x+1) || '' AND s2.str_pos_y1 = '' || (var_y-1) || '')  OR  
       (s1.str_pos_x1 = '' || (var_x-1) || '' AND s2.str_pos_y0 = '' || (var_y+1) || '')  OR  
       (s1.str_pos_x1 = '' || (var_x+1) || '' AND s2.str_pos_y0 = '' || (var_y+1) || '')))'';

  -- RAISE NOTICE ''Query: %'', var_query;

  FOR var_ret IN EXECUTE var_query LOOP
    RETURN NEXT var_ret;
  END LOOP; 
  RETURN var_ret;
END;
' LANGUAGE 'plpgsql';
