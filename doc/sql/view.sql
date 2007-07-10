-- $Id: view.sql,v 1.1 2004/04/14 01:21:57 mmr Exp $

--/-/-/-/-/---------------------
-- Views
  -- Item
CREATE VIEW view_item AS
  SELECT
    i.ite_id,
    i.ite_code,
    bi.bui_id,
    bi.bui_ite_price    AS ite_price,
    bi.bui_ite_quantity AS ite_quantity
  FROM
    item i JOIN
    building_x_item bi ON (i.ite_id = bi.ite_id)
  WHERE
    i.ite_active = true;

  -- Power
CREATE VIEW view_power AS
  SELECT
    p.pow_id,
    p.pow_code,
    bp.bui_id,
    bp.bui_pow_price    AS pow_price,
    bp.bui_pow_quantity AS pow_quantity
  FROM
    power p JOIN
    building_x_power bp ON (p.pow_id = bp.pow_id)
  WHERE
    p.pow_active = true;

  -- Drink
CREATE VIEW view_drink AS
  SELECT
    d.dri_id,
    d.dri_code,
    bd.bui_id,
    bd.bui_dri_price    AS dri_price,
    bd.bui_dri_quantity AS dri_quantity
  FROM
    drink d JOIN
    building_x_drink bd ON (d.dri_id = bd.dri_id)
  WHERE
    d.dri_active = true;

  -- Building
CREATE VIEW view_building AS
  SELECT
    bt.bty_name,
    bt.bty_code,
    b.cit_id,
    b.bui_id,
    b.bui_code,
    b.bui_hold,
    b.bui_pos_x,
    b.bui_pos_y
  FROM
    building b JOIN
    building_type bt ON (b.bty_id = bt.bty_id);

  -- NPC
CREATE VIEW view_npc AS
  SELECT
    nt.nty_name,
    nt.nty_code,
    nt.nty_blood,
    n.cit_id,
    n.npc_id,
    n.npc_pos_x,
    n.npc_pos_y
  FROM
    npc n JOIN
    npc_type nt ON (n.nty_id = nt.nty_id);

