-- #!sqlite
-- #{ flype
-- #  { init
CREATE TABLE IF NOT EXISTS flype_players (
	uuid VARCHAR(36) NOT NULL PRIMARY KEY,
	username VARCHAR(16) NOT NULL,
	flightState bool NOT NULL
);
-- #  }

-- #  { load
-- #      :uuid string
SELECT
	*
FROM flype_players
WHERE uuid = :uuid;
-- #  }

-- #  { create
-- #      :uuid string
-- #      :username string
INSERT OR IGNORE INTO flype_players (
	uuid,
	username,
	flightState
) VALUES (
	:uuid,
	:username,
	:flightState
);
-- #  }

-- #    { update
-- #      :uuid string
-- #      :username string
-- #      :flightState bool
UPDATE flype_players
SET username=:username,
	flightState=:flightState
WHERE uuid = :uuid;
-- #    }
-- # } 