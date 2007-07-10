-- $Id: drop.sql,v 1.1 2004/04/14 01:21:57 mmr Exp $

--/-/-/-/-/---------------------
-- Drops
  -- Functions
DROP FUNCTION func_mapGetBuildings(int, int, int);
DROP FUNCTION func_mapGetPlayers(int, int, int, int, interval);
DROP FUNCTION func_mapGetNPCs(int, int, int);
DROP FUNCTION func_mapGetStreetJunctions(int, int, int);
DROP FUNCTION func_newPlayer(text, text, text, int);

  -- Views
DROP VIEW
  view_item,
  view_power,
  view_drink,
  view_building,
  view_npc;

  -- Tables
DROP TABLE
  building,
  building_type,
  building_x_drink,
  building_x_item,
  building_x_power,
  city,
  clan,
  drink,
  forum_board,
  forum_message,
  item,
  log,
  message,
  npc,
  npc_type,
  player,
  player_x_item,
  player_x_power,
  power,
  ranking,
  street CASCADE;
