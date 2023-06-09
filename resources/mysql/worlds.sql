-- #!mysql

-- # {worlds
-- #    {init
CREATE TABLE IF NOT EXISTS Worlds (
                                    name VARCHAR(50) PRIMARY KEY,
                                    data LONGBLOB
);
-- #    }
-- #    {get
-- #        :world string
SELECT data FROM Worlds WHERE LOWER(`name`) = :world;
-- #    }
-- #    {update
-- #        :name string
-- #        :data string
REPLACE INTO Worlds (name, data)
VALUES (:name, :data);
-- #    }
-- #    {delete
-- #        :name string
DELETE FROM Worlds WHERE name=:name;
-- #    }
-- # }