-- $Id: fk.sql,v 1.1 2004/04/14 01:21:57 mmr Exp $

--/-/-/-/-/---------------------
-- Foreign Keys
  -- Player
    -- Player
ALTER TABLE player ADD
  FOREIGN KEY (cit_id) REFERENCES city (cit_id)
  ON DELETE NO ACTION
  ON UPDATE CASCADE;

ALTER TABLE player ADD
  FOREIGN KEY (cla_id) REFERENCES clan (cla_id)
  ON DELETE NO ACTION
  ON UPDATE CASCADE;

ALTER TABLE player ADD
  FOREIGN KEY (pla_parent_id) REFERENCES player (pla_id)
  ON DELETE NO ACTION
  ON UPDATE CASCADE;

    -- Clan
ALTER TABLE clan ADD
  FOREIGN KEY (pla_leader_id) REFERENCES player (pla_id)
  ON DELETE SET NULL
  ON UPDATE CASCADE;

ALTER TABLE clan ADD
  FOREIGN KEY (pla_pager_id) REFERENCES player (pla_id)
  ON DELETE SET NULL
  ON UPDATE CASCADE;

    -- Log
ALTER TABLE log ADD
  FOREIGN KEY (pla_id) REFERENCES player (pla_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

    -- Stuff
      -- Items
ALTER TABLE player_x_item ADD
  FOREIGN KEY (pla_id) REFERENCES player (pla_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE player_x_item ADD
  FOREIGN KEY (ite_id) REFERENCES item (ite_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE building_x_item ADD
  FOREIGN KEY (ite_id) REFERENCES item (ite_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE building_x_item ADD
  FOREIGN KEY (bui_id) REFERENCES building (bui_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

      -- Drinks
ALTER TABLE building_x_drink ADD
  FOREIGN KEY (dri_id) REFERENCES drink (dri_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE building_x_drink ADD
  FOREIGN KEY (bui_id) REFERENCES building (bui_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

      -- Powers
ALTER TABLE player_x_power ADD
  FOREIGN KEY (pla_id) REFERENCES player (pla_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE player_x_power ADD
  FOREIGN KEY (pow_id) REFERENCES power (pow_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE building_x_power ADD
  FOREIGN KEY (pow_id) REFERENCES power (pow_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE building_x_power ADD
  FOREIGN KEY (bui_id) REFERENCES building (bui_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

  -- City
    -- Street
ALTER TABLE street ADD
  FOREIGN KEY (cit_id) REFERENCES city (cit_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

    -- Building
ALTER TABLE building ADD
  FOREIGN KEY (cit_id) REFERENCES city (cit_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE building ADD
  FOREIGN KEY (bty_id) REFERENCES building_type (bty_id)
  ON DELETE NO ACTION
  ON UPDATE CASCADE;

    -- NPC
ALTER TABLE npc ADD
  FOREIGN KEY (cit_id) REFERENCES city (cit_id)
  ON DELETE NO ACTION
  ON UPDATE CASCADE;

ALTER TABLE npc ADD
  FOREIGN KEY (nty_id) REFERENCES npc_type (nty_id)
  ON DELETE NO ACTION
  ON UPDATE CASCADE;

  -- Communication
    -- Message (player <-> player)
ALTER TABLE message ADD
  FOREIGN KEY (pla_from_id) REFERENCES player (pla_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE message ADD
  FOREIGN KEY (pla_to_id) REFERENCES player (pla_id)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

    -- Forum
ALTER TABLE forum_board ADD
  FOREIGN KEY (pla_id) REFERENCES player (pla_id)
  ON DELETE NO ACTION
  ON UPDATE CASCADE;

ALTER TABLE forum_board ADD
  FOREIGN KEY (cla_id) REFERENCES clan (cla_id)
  ON DELETE NO ACTION
  ON UPDATE CASCADE;

ALTER TABLE forum_message ADD
  FOREIGN KEY (fbr_id) REFERENCES forum_board (fbr_id)
  ON DELETE CASCADE 
  ON UPDATE CASCADE;

ALTER TABLE forum_message ADD
  FOREIGN KEY (pla_id) REFERENCES player (pla_id)
  ON DELETE CASCADE 
  ON UPDATE CASCADE;
