-- #!mysql

-- # {kits
-- #    {init
CREATE TABLE IF NOT EXISTS Kits (
    username VARCHAR(50) PRIMARY KEY,
    cooldowns JSON
);
-- #    }
-- #    {get
-- #        :username string
SELECT cooldowns FROM Kits WHERE LOWER(`username`) = :username;
-- #    }
-- #    { update
-- #        :username string
-- #        :cooldowns string
REPLACE INTO Kits (username, cooldowns)
VALUES (:username, :cooldowns);
-- #    }
-- # }