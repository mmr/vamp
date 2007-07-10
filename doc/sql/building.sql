-- $Id: building.sql,v 1.2 2004/04/14 07:04:39 mmr Exp $

--/-/-/-/-/---------------------
-- Temporary Functions 
-- I strongly advise you to dont read the code below
-- It was wrote just to make my life (when changing stuff) easier

-- Configuring Buildings
CREATE FUNCTION func_tmp_configureGuild(int,int) RETURNS VOID AS '
DECLARE
  id  ALIAS FOR $1;
  qt  ALIAS FOR $2;
  xr  record;
  x   int;
  i   int;
  aux text;
BEGIN
  aux := ''0'';
  FOR i IN 0..qt LOOP
    SELECT INTO xr pow_id FROM power WHERE pow_id NOT IN (aux) LIMIT 1;
    x := xr.pow_id;

    INSERT INTO building_x_power (
      bui_id, pow_id, bui_pow_price, bui_pow_quantity
    )
    VALUES (
      id, x, (x*(random()*100))::int, (random()*100/x)::int
    );
    aux := aux || '' '' || x;
  END LOOP;
END;'
LANGUAGE 'plpgsql';

CREATE FUNCTION func_tmp_configureShop(int,int) RETURNS VOID AS '
DECLARE
  id  ALIAS FOR $1;
  qt  ALIAS FOR $2;
  x   int;
  i   int;
  aux text;
BEGIN
  aux := ''0'';
  FOR i IN 0..qt LOOP
    SELECT INTO x ite_id FROM item WHERE ite_id NOT IN (aux) LIMIT 1;
    INSERT INTO building_x_item (
      bui_id, ite_id, bui_ite_price, bui_ite_quantity
    )
    VALUES (
      id, x.ite_id, (x*(random()*100))::int, (random()*100/x)::int
    );
    aux := aux || '' '' || x;
  END LOOP;
END;'
LANGUAGE 'plpgsql';

CREATE FUNCTION func_tmp_configurePub(int,int) RETURNS VOID AS '
DECLARE
  id  ALIAS FOR $1;
  qt  ALIAS FOR $2;
  x int;
  i   int;
  aux text;
BEGIN
  aux := ''0'';
  FOR i IN 0..qt LOOP
    SELECT INTO x dri_id FROM drink WHERE dri_id NOT IN (aux) LIMIT 1;
    INSERT INTO building_x_drink (
      bui_id, dri_id, bui_dri_price, bui_dri_quantity
    )
    VALUES (
      id, x, (x*(random()*100))::int, (random()*100/x)::int
    );
    aux := aux || '' '' || x;
  END LOOP;
END;'
LANGUAGE 'plpgsql';

--
-- Create Buildings
--

-- Temporary Table
CREATE TABLE tmp_valid_positions (
  pos_id SERIAL NOT NULL PRIMARY KEY,
  pos_x INT NOT NULL,
  pos_y INT NOT NULL
);

-- This function randomly allocates buildings in a given city, according to a
  -- percentage criteria
-- func_tmp_createBuildings(cid, pguilds, pshops, pbanks, ppubs, phouses)  
  -- cid  = id of the city
  -- pguilds  = percentage of guilds in this city
  -- pbanks   = percentage of banks
  -- ppubs    = percentage of pubs
  -- phouses  = percentage of houses (it is used just to see if the sum is valid)
  -- Obviouslly the percentages should sum up to 100% :P

CREATE OR REPLACE FUNCTION func_tmp_createBuildings(int, int, int, int, int, int) RETURNS VOID AS '
DECLARE
  -- Args
  cid     ALIAS FOR $1;
  pguilds ALIAS FOR $2;
  pshops  ALIAS FOR $3;
  pbanks  ALIAS FOR $4;
  ppubs   ALIAS FOR $5;
  phouses ALIAS FOR $6;

  -- Auxiliar Vars
    -- Counters
  i int;  -- Horizontal Counter
  j int;  -- Vertical   Counter
  k int;  -- Squares    Counter
  m int;  -- Namer      Counter (shop|pub|guild)

    -- Calc
  cguilds int;
  cshops  int;
  cbanks  int;
  cpubs   int;

    -- Data
  id  int;  -- Building ID
  bty int;  -- Building Type
  pos record; -- Position (from the valid positions)
  cit record; -- x0, y0, x1, y0 data from city
  total int;  -- Total of possible buildings in the city

    -- Aux
  aux text;
  old text;
BEGIN
  -- Seeing if the user can sum
  i := pguilds + pshops + pbanks + ppubs + phouses;
  IF i <> 100 THEN 
    RAISE NOTICE ''Learn how to sum.'';
    RETURN;
  END IF;

  -- Getting Data from City
  SELECT INTO cit
    cit_pos_x0 AS x0,
    cit_pos_y0 AS y0, 
    cit_pos_x1 AS x1,
    cit_pos_y1 AS y1,
    SUBSTR(cit_code, 5) AS code
  FROM
    city
  WHERE
    cit_id = cid;

  IF NOT FOUND THEN
    RAISE NOTICE ''Could not find city %, aborting.'', cid;
    RETURN;
  END IF;

  -- Calculating total of possible buildings for this city
  total := (((cit.x1 - cit.x0 + 2)/2)-1) * (((cit.y1 - cit.y0 + 2)/2)-1);

  -- Calculating percentages
  cguilds := ROUND(total*pguilds/100::float);
  cshops  := cguilds  + ROUND(total*pshops/100::float);
  cbanks  := cshops   + ROUND(total*pbanks/100::float);
  cpubs   := cbanks   + ROUND(total*ppubs/100::float);

  --RAISE NOTICE ''cid,x0,y0,x1,y1,total = %,%,%,%,%,%'',cid,cit.x0,cit.y0,cit.x1,cit.y1,total;
  --RAISE NOTICE ''pguilds,pshops,pbanks,ppubs = %(%),%(%),%(%),%(%)'',cguilds,pguilds,cshops,pshops,cbanks,pbanks,cpubs,ppubs;

  -- Allocating Positions
  DELETE FROM tmp_valid_positions;
  FOR i IN (cit.x0 + 1)..(cit.x1) LOOP
    FOR j IN (cit.y0 + 1)..(cit.y1) LOOP
      INSERT INTO tmp_valid_positions (pos_x, pos_y) VALUES (i, j);
      RAISE NOTICE ''INSERT tmp_valid_positions (%, %)'', i, j;
      j := j + 1;
    END LOOP;
    i := i + 1;
  END LOOP;

  -- Deleting Buildings from City
  DELETE FROM building WHERE cit_id = cid;

  old := ''guild'';

  -- Setting types
  k := 1;
  m := 1;
  FOR i IN (cit.x0 + 1)..(cit.x1) LOOP
    FOR j IN (cit.y0 + 1)..(cit.y1) LOOP
      IF k <= cguilds THEN
        aux := ''guild'';
        bty := 4;
      ELSIF k <= cshops THEN
        aux := ''shop'';
        bty := 2;
      ELSIF k <= cbanks THEN
        aux := ''bank'';
        bty := 3;
      ELSIF k <= cpubs THEN
        aux := ''pub'';
        bty := 5;
      ELSE
        aux := ''house'';
        bty := 1;
      END IF;

      IF old <> aux THEN
        m := 1;
      END IF;

      old := aux;
      aux := ''bui_'' || aux || ''_'' || cit.code || ''_'' || m;

      -- Getting random position from valid positions
      SELECT INTO pos pos_id, pos_x, pos_y FROM tmp_valid_positions ORDER BY random() LIMIT 1;

      IF FOUND THEN
        -- Deleting position
        DELETE FROM tmp_valid_positions WHERE pos_id = pos.pos_id;

        -- Getting Building ID
        SELECT INTO id NEXTVAL(''building_bui_id_seq'');

        -- Inserting building in position
        INSERT INTO building
          (bui_id, cit_id, bty_id, bui_pos_x, bui_pos_y, bui_code)
        VALUES
          (id, cid, bty, pos.pos_x, pos.pos_y, aux);

        RAISE NOTICE ''INSERT building (%, %, %, %, %, %)'', id, cid, bty, pos.pos_x, pos.pos_y, aux;

        -- Configuring Building
        IF bty = 2 THEN     -- guild
          --SELECT func_tmp_configureGuild(id, 5);
        ELSIF bty = 3 THEN  -- shop
          --SELECT func_tmp_configureShop(id, 10);
        ELSIF bty = 5 THEN  -- pub
          --SELECT func_tmp_configurePub(id, 20);
        END IF;
      END IF;

      -- Incrementing counters
      j := j + 1;
      m := m + 1;
      k := k + 1;
    END LOOP;
    i := i + 1;
  END LOOP;
  RETURN;
END;'
LANGUAGE 'plpgsql';


SELECT func_tmp_createBuildings(1, 2, 3, 4, 5, 86);
SELECT func_tmp_createBuildings(2, 2, 5, 5, 5, 83);
DROP FUNCTION func_tmp_configureGuild(int,int);
DROP FUNCTION func_tmp_configureShop(int,int);
DROP FUNCTION func_tmp_configurePub(int,int);
DROP FUNCTION func_tmp_createBuildings(int, int, int, int, int, int);
DROP TABLE tmp_valid_positions CASCADE;
