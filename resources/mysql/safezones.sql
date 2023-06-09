-- #!mysql

-- # {safezones
-- #    {init
CREATE TABLE IF NOT EXISTS SafeZones (
                                      name VARCHAR(50) PRIMARY KEY,
                                      world VARCHAR(50),
                                      pos1 BLOB,
                                      pos2 BLOB
);

-- #    }
-- #    {get
-- #        :name string
SELECT * FROM SafeZones WHERE LOWER(`name`) = :name;
-- #    }

-- #    {getAll
SELECT * FROM SafeZones;
-- #    }

-- #    {update
-- #        :name string
-- #        :world string
-- #        :pos1 string
-- #        :pos2 string
REPLACE INTO SafeZones (name, world, pos1, pos2)
VALUES (:name, :world, :pos1, :pos2);
-- #    }

-- #    {delete
-- #        :name string
DELETE FROM SafeZones WHERE LOWER(`name`) = :name;
-- #    }

-- # }