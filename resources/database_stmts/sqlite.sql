-- #!sqlite
-- #{ flype
-- #  { init
CREATE TABLE IF NOT EXISTS flype_players (
	uuid VARCHAR(36) NOT NULL PRIMARY KEY,
	username VARCHAR(16) NOT NULL,
	flightState bool DEFAULT false,
	flightSound string DEFAULT 'XpCollectSound',
	flightParticle string DEFAULT 'FlameParticle',
	flightEffect string DEFAULT '',
	flightCape string DEFAULT '',
	flightTime int DEFAULT 0
);

-- #  }
-- #  { load
-- #      :uuid string
SELECT *
FROM flype_players
WHERE uuid = :uuid;

-- #  }
-- #  { create
-- #      :uuid string
-- #      :username string
INSERT OR IGNORE INTO flype_players (
	uuid,
	username
) VALUES (
	:uuid,
	:username
);

-- #  }
-- #  { update
-- #      :uuid string
-- #      :username string
-- #      :flightState bool
-- #      :flightSound string
-- #      :flightParticle string
-- #      :flightEffect string
-- #      :flightCape string
-- #      :flightTime int
UPDATE flype_players
SET username = :username,
	flightState = :flightState,
	flightSound = :flightSound,
	flightParticle = :flightParticle,
	flightEffect = :flightEffect,
	flightCape = :flightCape,
	flightTime = :flightTime
WHERE uuid = :uuid;

-- #  }
-- # }