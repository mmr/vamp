-- $Id: city.sql,v 1.3 2004/04/14 08:19:07 mmr Exp $

--/-/-/-/-/---------------------
-- Temporary Functions 
-- I strongly advise you to do not read the code below
-- It was wrote just to make my life (when changing stuff) easier

-- City
  -- City Abstract: NxN
  -- NxN = N streets crossed by N streets, leaving 1 space between each 
    -- horizontal or vertical line for a building
  -- That means a street (horizontal or vertical) takes twice its original space
    -- The map doesnt end with buildings, so we need a -1
    -- So, that is it: (N*2)-1 x (N*2)-1
  -- We have ((N*2)-1)^2 valid squares/positions
    -- From this, (((N*2)-1) * N) + (N*(N-1)) are streets
    -- And ((N-1) * (N-1)) are buildings (or just (total - streets))

  --  That is what the map looks like:
  --   
  --   ---------.---------.---------
  --  |         |         |         |
  --  | X-1,Y-1 |  X,Y-1  | X+1,Y-1 |
  --  |         |         |         |
  --  |---------|---------|---------|
  --  |         |         |         |
  --  |  X-1,Y  |   X,Y   |  X+1,Y  |
  --  |         |         |         |
  --  |---------|---------|---------|
  --  |         |         |         |
  --  | X-1,Y+1 |  X,Y+1  | X+1,Y+1 |
  --  |         |         |         |
  --   ---------^---------^---------
  -- The player always stands by X,Y (sounds logical)


-- Example City 1: 25x25
  -- 25x25 = (25*2)-1 x (25*2)-1 = (49x49) = (0-48x0-48)
  -- We have 49^2 valid squares/positions = (2401)
    -- From this 49^2 squares, (49*25)+(25*(25-1)) are streets = 1825
    -- And ((25-1) * (25-1)) = 2401. 2401 - 1825 = 576 buildings
-- That would create it
-- INSERT INTO city (cit_pos_x0, cit_pos_y0, cit_pos_x1, cit_pos_y1, cit_code) VALUES (0, 0, 48, 48, 'cit_1');

-- Heres a temporary function for making easy the job to create a new city
--
-- Create City
-- func_tmp_createCity(name, x0, y0, x1, y1);
-- name = number that identifies the city (for naming in the lang file)
  -- must be unique
CREATE FUNCTION func_tmp_createCity(int, int, int, int, int) RETURNS VOID AS '
DECLARE
  cit ALIAS FOR $1;
  x0  ALIAS FOR $2;
  y0  ALIAS FOR $3;
  x1  ALIAS FOR $4;
  y1  ALIAS FOR $5;
  query   text;

  i int;  -- Horizontal Counter
  j int;  -- Vertical   Counter
  id  int;  -- City Id
BEGIN
  j := 1;

  -- Creating City
    -- Getting cit_id
  SELECT INTO id NEXTVAL(''city_cit_id_seq'');

  query := ''
    INSERT INTO city (
      cit_id,
      cit_code,
      cit_pos_x0, cit_pos_y0,
      cit_pos_x1, cit_pos_y1
    )
    VALUES (
      '' || id || '', 
      ''''cit_'' || cit || '''''',
      '' || x0 || '',
      '' || y0 || '',
      '' || x1 || '',
      '' || y1 || ''
    )'';

  RAISE NOTICE ''Query: %'', query;
  EXECUTE query;

  -- Horizontal Streets
  FOR i IN y0..y1 LOOP
    query := ''
      INSERT INTO street (
        cit_id,
        str_pos_x0, str_pos_y0,
        str_pos_x1, str_pos_y1,
        str_code
      )
      VALUES (
        '' || id || '',
        '' || x0 || '',
        '' || i  || '',
        '' || x1 || '',
        '' || i  || '',
        ''''str_'' || cit || ''_'' || j || ''''''
      )'';

    RAISE NOTICE ''Query: %'', query;
    EXECUTE query;

    i := i + 1;
    j := j + 1;
  END LOOP;

  -- Vertical Streets
  FOR i IN x0..x1 LOOP
    query := ''
      INSERT INTO street (
        cit_id,
        str_pos_x0, str_pos_y0,
        str_pos_x1, str_pos_y1,
        str_code
      )
      VALUES (
        '' || id || '',
        '' || i  || '',
        '' || y0 || '',
        '' || i  || '',
        '' || y1 || '',
        ''''str_'' || cit || ''_'' || j || ''''''
      )'';

    RAISE NOTICE ''Query: %'', query;
    EXECUTE query;

    i := i + 1;
    j := j + 1;
  END LOOP;
  RETURN;
END;'
LANGUAGE 'plpgsql';

SELECT func_tmp_createCity(1, 0, 0, 48, 48);  -- 25x25
SELECT func_tmp_createCity(2, 50, 0, 74, 48); -- 13x25
-- func_tmp_createCity(name, x0, y0, x1, y1);
DROP FUNCTION func_tmp_createCity(int, int, int, int, int);
