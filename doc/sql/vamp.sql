-- $Id: vamp.sql,v 1.26 2004/04/14 05:46:48 mmr Exp $ Vamp

--/-/-/-/-/---------------------
-- This is the main file, it should be called to recreate the game database's structure

-- Dropping
\i drop.sql

-- Tables
\i table.sql

-- Foreign Keys
\i fk.sql

-- Views
\i view.sql

-- Functions
\i func.sql

-- System Data
\i data.sql
