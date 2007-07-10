-- $Id: npcsandplayers.sql,v 1.6 2004/04/16 22:25:38 mmr Exp $

--/-/-/-/-/---------------------
-- Temporary Functions 
-- I strongly advise you to dont read the code below
-- It was wrote just to make my life (when changing stuff) easier

-- 
-- Create NPCs
--
CREATE OR REPLACE FUNCTION func_tmp_createNPCs(int, int, int) RETURNS VOID AS '
DECLARE
  var_cit_id  ALIAS FOR $1;
  var_nty_id  ALIAS FOR $2;
  var_npcs    ALIAS FOR $3;
  i INT;
  r record; -- random position
  c record; -- city data
BEGIN
  -- Getting data from City
  SELECT INTO c
    cit_pos_x0 AS x0,
    cit_pos_y0 AS y0, 
    cit_pos_x1 AS x1,
    cit_pos_y1 AS y1
  FROM
    city
  WHERE
    cit_id = var_cit_id;

  IF NOT FOUND THEN
    RAISE NOTICE ''Could not find city %, aborting.'', var_cit_id;
    RETURN;
  END IF;

  -- Deleting NPCs of the city
  DELETE FROM npc WHERE cit_id = var_cit_id;

  i := 0;
  WHILE i < var_npcs LOOP
    SELECT INTO r
      func_b1nRandom(c.x0, c.x1) AS x,
      func_b1nRandom(c.y0, c.y1) AS y;

    RAISE NOTICE ''INSERT npc (%, %, %, %)'', var_cit_id, var_nty_id, r.x, r.y;

    INSERT INTO npc
      (cit_id, nty_id, npc_pos_x, npc_pos_y)
    VALUES
      (var_cit_id, var_nty_id, r.x, r.y);
    i := i + 1;
  END LOOP;
  RETURN; 
END;'
LANGUAGE 'plpgsql';

--
-- Create Pseudo Players
-- Obs: The Default password for Pseudo Players is 1234
--
CREATE OR REPLACE FUNCTION func_tmp_createPseudoPlayers(int, int) RETURNS VOID AS '
DECLARE
  var_cit_id  ALIAS FOR $1;
  var_players ALIAS FOR $2;
  i int;
  r record;   -- random position
  c record;   -- city data
  p record;   -- last pseudo 
  var_next  int;  -- next pseudo
  aux   int;  -- Aux var
  query text;
BEGIN
  -- Getting data from City
  SELECT INTO c
    cit_pos_x0 AS x0,
    cit_pos_y0 AS y0, 
    cit_pos_x1 AS x1,
    cit_pos_y1 AS y1
  FROM
    city
  WHERE
    cit_id = var_cit_id;

  IF NOT FOUND THEN
    RAISE NOTICE ''Could not find city %, aborting.'', var_cit_id;
    RETURN;
  END IF;

  -- Deleting all pseudo-players of city
  DELETE FROM player 
  WHERE
    cit_id = var_cit_id AND
    pla_login LIKE ''pseudo%'' AND
    pla_email LIKE ''pseudo%@b1n.org'';

  -- Checking what is the next pseudo player based on the last one
  SELECT INTO p 
    SUBSTR(pla_login, 7)::int AS last
  FROM
    player
  WHERE
    pla_login LIKE ''pseudo%'' AND
    pla_email LIKE ''pseudo%@b1n.org''
  ORDER BY pla_login DESC
  LIMIT 1;

  IF FOUND THEN
    var_next := p.last + 1;
  ELSE
    var_next := 0; 
  END IF;

  -- Creating Pseudo Players
  i := var_next;
  aux := i + var_players;
  WHILE i < aux LOOP
    SELECT INTO r
      func_b1nRandom(c.x0, c.x1) AS x,
      func_b1nRandom(c.y0, c.y1) AS y;

    query := ''
      INSERT INTO player
        (cit_id, pla_login, pla_passwd, pla_email, pla_exp, pla_active, pla_pos_x, pla_pos_y)
      VALUES ( 
        '' || var_cit_id || '',
        ''''pseudo''  || i || '''''',
        ''''E/DmS3Y2Yh7+0cuxg5moaceElx7i2rILJSGLoUgqzhw='''',
        ''''pseudo''  || i || ''@b1n.org'''',
        '' || func_b1nRandom(1000) || '', true, '' || r.x || '', '' || r.y || ''
      )'';

    RAISE NOTICE ''%'', query;
    EXECUTE query;
    i := i + 1;
  END LOOP;
  RETURN; 
END;'
LANGUAGE 'plpgsql';

--SELECT func_tmp_createNPCs(1, 1, 500);
--SELECT func_tmp_createNPCs(2, 1, 500);
SELECT func_tmp_createPseudoPlayers(1, 100);
SELECT func_tmp_createPseudoPlayers(2, 50);
DROP FUNCTION func_tmp_createNPCs(int, int, int);
DROP FUNCTION func_tmp_createPseudoPlayers(int, int);
