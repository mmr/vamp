-- $Id: session.sql,v 1.3 2004/04/14 01:21:57 mmr Exp $

--/-/-/-/-/---------------------
-- Session Management

DROP TABLE session CASCADE;
CREATE TABLE session (
  ses_id    CHAR(32)  NOT NULL PRIMARY KEY,
  ses_ip    TEXT  NOT NULL,
  ses_data  TEXT  NULL,
  ses_add_dt      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ses_last_updated  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
