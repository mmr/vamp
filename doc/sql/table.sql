-- $Id: table.sql,v 1.1 2004/04/14 01:21:57 mmr Exp $

--/-/-/-/-/---------------------
-- Tables
-- General

-- Player
  -- Stuff
CREATE TABLE power (
  pow_id        SERIAL  NOT NULL  PRIMARY KEY,
  pow_name      TEXT    NULL,
  pow_recharge  BOOLEAN NOT NULL  DEFAULT false, -- Is Rechargeable
  pow_active    BOOLEAN NOT NULL  DEFAULT true,
  pow_code      TEXT    NOT NULL  UNIQUE,
  pow_add_dt    TIMESTAMP NOT NULL  DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE item (
  ite_id        SERIAL  NOT NULL  PRIMARY KEY,
  ite_name      TEXT    NULL,
  ite_recharge  BOOLEAN NOT NULL  DEFAULT false, -- Is Rechargeable
  ite_active    BOOLEAN NOT NULL  DEFAULT true,
  ite_code      TEXT    NOT NULL  UNIQUE,
  ite_add_dt    TIMESTAMP NOT NULL  DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE drink (
  dri_id        SERIAL  NOT NULL  PRIMARY KEY,
  dri_name      TEXT    NULL,
  dri_chance    INT     NOT NULL  DEFAULT 50, -- Chance percentage of barman give a hint about the game
  dri_exp       INT     NOT NULL  DEFAULT 0,
  dri_active    BOOLEAN NOT NULL  DEFAULT true,
  dri_code      TEXT    NOT NULL  UNIQUE,
  dri_add_dt    TIMESTAMP NOT NULL  DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE ranking (
  ran_id        SERIAL  NOT NULL PRIMARY KEY,
  ran_exp       INT     NOT NULL,
  ran_code      TEXT    NOT NULL,
  ran_add_dt    TIMESTAMP NOT NULL  DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE log (
  pla_id        INT   NOT NULL, -- FK player

  log_id        SERIAL  NOT NULL  PRIMARY KEY,
  log_action    TEXT    NOT NULL,
  log_vars      TEXT    NULL,
  log_show      BOOLEAN NOT NULL  DEFAULT true, -- Whether this will appear in player's history
  log_add_dt    TIMESTAMP NOT NULL  DEFAULT CURRENT_TIMESTAMP
);

    -- Player
CREATE TABLE player (
  cit_id        INT NOT NULL, -- FK city
  cla_id        INT NULL,     -- FK clan
  pla_parent_id INT NULL,     -- FK player (Parent)

  pla_id        SERIAL  NOT NULL PRIMARY KEY,
  pla_login     TEXT  NOT NULL UNIQUE,
  pla_passwd    TEXT  NOT NULL,
  pla_email     TEXT  NOT NULL,
  pla_money     INT   NOT NULL  DEFAULT 0,
  pla_bank_money  INT NOT NULL  DEFAULT 0,
  pla_exp       INT   NOT NULL  DEFAULT 3, -- Experience
  pla_pos_x     INT   NOT NULL  DEFAULT 0,
  pla_pos_y     INT   NOT NULL  DEFAULT 0,
  pla_active    BOOLEAN NOT NULL  DEFAULT true,
  pla_action_points INT NOT NULL  DEFAULT 50,
  pla_last_login  TIMESTAMP NOT NULL  DEFAULT CURRENT_TIMESTAMP,
  pla_add_dt      TIMESTAMP NOT NULL  DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE clan (
  pla_leader_id INT NULL, -- FK player (Leader)
  pla_pager_id  INT NULL, -- FK player (Forum Pager)

  cla_id        SERIAL  NOT NULL PRIMARY KEY,
  cla_name      TEXT  NOT NULL,
  cla_sign      TEXT  NULL,
  pla_add_dt    TIMESTAMP NOT NULL  DEFAULT CURRENT_TIMESTAMP
);
  
  -- Relationships
CREATE TABLE player_x_item (
  pla_id        INT NOT NULL, -- FK player
  ite_id        INT NOT NULL, -- FK item

  pla_ite_quantity  INT NOT NULL  DEFAULT 1,
  pla_ite_add_dt  TIMESTAMP NOT NULL  DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(pla_id, ite_id)
);

CREATE TABLE player_x_power (
  pla_id        INT NOT NULL, -- FK player
  pow_id        INT NOT NULL, -- FK power

  pla_pow_quantity  INT NOT NULL  DEFAULT 1,
  pla_pow_add_dt  TIMESTAMP NOT NULL  DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(pla_id, pow_id)
);

CREATE TABLE building_x_item (
  bui_id        INT NOT NULL, -- FK building
  ite_id        INT NOT NULL, -- FK item

  bui_ite_price     INT NOT NULL  DEFAULT 1,
  bui_ite_quantity  INT NOT NULL  DEFAULT 1,
  bui_ite_add_dt  TIMESTAMP NOT NULL  DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(bui_id, ite_id)
);

CREATE TABLE building_x_drink (
  bui_id        INT NOT NULL, -- FK building
  dri_id        INT NOT NULL, -- FK drink

  bui_dri_price     INT NOT NULL  DEFAULT 1,
  bui_dri_quantity  INT NOT NULL  DEFAULT 1,
  bui_dri_add_dt  TIMESTAMP NOT NULL  DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(bui_id, dri_id)
);

CREATE TABLE building_x_power (
  bui_id        INT NOT NULL, -- FK building
  pow_id        INT NOT NULL, -- FK power

  bui_pow_price     INT NOT NULL  DEFAULT 1,
  bui_pow_quantity  INT NOT NULL  DEFAULT 1,
  bui_pow_add_dt  TIMESTAMP NOT NULL  DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(bui_id, pow_id)
);

-- City
CREATE TABLE city (
  cit_id        SERIAL  NOT NULL PRIMARY KEY,
  cit_pos_x0    INT   NOT NULL,
  cit_pos_y0    INT   NOT NULL,
  cit_pos_x1    INT   NOT NULL,
  cit_pos_y1    INT   NOT NULL,
  cit_code      TEXT  NOT NULL  UNIQUE,
  cit_add_dt    TIMESTAMP NOT NULL  DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE street (
  cit_id        INT   NOT NULL, -- FK city

  str_id        SERIAL  NOT NULL  PRIMARY KEY,
  str_pos_x0    INT   NOT NULL,
  str_pos_y0    INT   NOT NULL,
  str_pos_x1    INT   NOT NULL,
  str_pos_y1    INT   NOT NULL,
  str_code      TEXT  NOT NULL  UNIQUE,
  str_add_dt    TIMESTAMP NOT NULL  DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE building_type (
  bty_id        SERIAL  NOT NULL  PRIMARY KEY,
  bty_name      TEXT    NOT NULL,
  bty_canask    BOOLEAN NOT NULL  DEFAULT true, -- Whether can ask NPC
  bty_code      TEXT    NOT NULL  UNIQUE,
  bty_add_dt    TIMESTAMP NOT NULL  DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE building (
  cit_id        INT   NOT NULL, -- FK city
  bty_id        INT   NOT NULL, -- FK building_type

  bui_id        SERIAL  NOT NULL  PRIMARY KEY,
  bui_pos_x     INT     NOT NULL,
  bui_pos_y     INT     NOT NULL,
  bui_hold      INT     NOT NULL  DEFAULT 10,
  bui_code      TEXT    NOT NULL  UNIQUE,      
  bui_add_dt    TIMESTAMP NOT NULL  DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(bui_pos_x, bui_pos_y)
);

  -- Non Playable Character
CREATE TABLE npc_type (
  nty_id        SERIAL  NOT NULL  PRIMARY KEY,
  nty_name      TEXT    NOT NULL,
  nty_havemoney BOOLEAN NOT NULL  DEFAULT true,
  nty_blood     CHAR(4) NOT NULL  DEFAULT '1:5', -- Blood points min:max
  nty_code      TEXT    NOT NULL  UNIQUE,
  nty_add_dt    TIMESTAMP NOT NULL  DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE npc (
  cit_id        INT     NOT NULL, -- FK city
  nty_id        INT     NOT NULL, -- FK npc_type

  npc_id        SERIAL  NOT NULL PRIMARY KEY,
  npc_name      TEXT    NULL,
  npc_pos_x     INT     NOT NULL,
  npc_pos_y     INT     NOT NULL,
  npc_add_dt    TIMESTAMP NOT NULL  DEFAULT CURRENT_TIMESTAMP     
);

-- Communication
CREATE TABLE message (
  pla_from_id   INT NOT NULL, -- FK player (sender)
  pla_to_id     INT NOT NULL, -- FK player (receiver)

  mes_id        SERIAL  NOT NULL PRIMARY KEY,
  mes_text      TEXT    NOT NULL,
  mes_add_dt    TIMESTAMP NOT NULL  DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE forum_board (
  cla_id        INT   NOT NULL, -- FK clan
  pla_id        INT   NOT NULL, -- FK player (creator)

  fbr_id        SERIAL  NOT NULL  PRIMARY KEY,
  fbr_name      TEXT  NOT NULL,
  fbr_msgs      INT   NOT NULL DEFAULT 0,
  fbr_add_dt    TIMESTAMP NOT NULL  DEFAULT CURRENT_TIMESTAMP,
  UNIQUE(cla_id, fbr_name)
);

CREATE TABLE forum_message (
  fbr_id        INT NOT NULL, -- FK forum_board
  pla_id        INT NOT NULL, -- FK player (sender)

  fms_id        SERIAL  NOT NULL  PRIMARY KEY,
  fms_text      TEXT    NOT NULL,
  fms_add_dt    TIMESTAMP NOT NULL  DEFAULT CURRENT_TIMESTAMP
);
