-- $Id: data.sql,v 1.1 2004/04/14 01:21:57 mmr Exp $

--/-/-/-/-/---------------------
-- System Data
  -- Building
    -- Type
INSERT INTO building_type (bty_code, bty_name, bty_canask) VALUES ('bty_house', 'House', false);
INSERT INTO building_type (bty_code, bty_name) VALUES ('bty_shop',  'Shop');
INSERT INTO building_type (bty_code, bty_name) VALUES ('bty_bank',  'Bank');
INSERT INTO building_type (bty_code, bty_name, bty_canask) VALUES ('bty_guild', 'Guild', false);
INSERT INTO building_type (bty_code, bty_name) VALUES ('bty_pub',   'Pub');

  -- NPC
    -- Type
INSERT INTO npc_type (nty_code, nty_name) VALUES ('nty_human', 'Human');

  -- Stuff
    -- Item
INSERT INTO item (ite_code, ite_name) VALUES ('ite_1', 'Scroll of Turning');
INSERT INTO item (ite_code, ite_name) VALUES ('ite_2', 'Holy Water');
INSERT INTO item (ite_code, ite_name) VALUES ('ite_3', 'Garlic');

    -- Power
INSERT INTO power (pow_code, pow_name) VALUES ('pow_1', 'Invisibility');
INSERT INTO power (pow_code, pow_name) VALUES ('pow_2', 'Thievery');
INSERT INTO power (pow_code, pow_name) VALUES ('pow_3', 'Hypnoses');

    -- Drink
INSERT INTO drink (dri_code, dri_chance) VALUES ('dri_1', 5);
INSERT INTO drink (dri_code, dri_chance) VALUES ('dri_2', 10);
INSERT INTO drink (dri_code, dri_chance) VALUES ('dri_3', 15);
INSERT INTO drink (dri_code, dri_chance) VALUES ('dri_4', 20);
INSERT INTO drink (dri_code, dri_chance) VALUES ('dri_5', 25);
INSERT INTO drink (dri_code, dri_chance) VALUES ('dri_6', 30);
INSERT INTO drink (dri_code, dri_chance) VALUES ('dri_7', 31);
INSERT INTO drink (dri_code, dri_chance) VALUES ('dri_8', 32);
INSERT INTO drink (dri_code, dri_chance) VALUES ('dri_9', 33);
INSERT INTO drink (dri_code, dri_chance) VALUES ('dri_10', 34);
INSERT INTO drink (dri_code, dri_chance) VALUES ('dri_11', 35);
INSERT INTO drink (dri_code, dri_chance) VALUES ('dri_12', 50);
INSERT INTO drink (dri_code, dri_chance) VALUES ('dri_13', 50);
INSERT INTO drink (dri_code, dri_chance, dri_exp) VALUES ('dri_14', 55, 1);
INSERT INTO drink (dri_code, dri_chance) VALUES ('dri_15', 55);
INSERT INTO drink (dri_code, dri_chance) VALUES ('dri_16', 60);
INSERT INTO drink (dri_code, dri_chance, dri_exp) VALUES ('dri_17', 65, 1);
INSERT INTO drink (dri_code, dri_chance, dri_exp) VALUES ('dri_18', 70, 2);

  -- Player
    -- Ranking
INSERT INTO ranking (ran_code, ran_exp) VALUES ('ran_1', 1);
INSERT INTO ranking (ran_code, ran_exp) VALUES ('ran_2', 5);
INSERT INTO ranking (ran_code, ran_exp) VALUES ('ran_3', 10);
