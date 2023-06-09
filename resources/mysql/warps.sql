-- #!mysql

-- # {warps
-- #    {init
CREATE TABLE IF NOT EXISTS Warps (
    name VARCHAR(150) PRIMARY KEY,
    world VARCHAR(50),
    server VARCHAR(50),
    pos BLOB,
    open BOOLEAN
);

-- #    }
-- #    {get
-- #        :name string
SELECT * FROM Warps WHERE LOWER(`name`) = :name;
-- #    }

-- #    {getAll
SELECT * FROM Warps;
-- #    }

-- #    {update
-- #        :name string
-- #        :world string
-- #        :server string
-- #        :pos string
-- #        :open bool
REPLACE INTO Warps (name, world, server, pos, open)
VALUES (:name, :world, :server, :pos, :open);
-- #    }

-- #    {delete
-- #        :name string
DELETE FROM Warps WHERE LOWER(`name`) = :name;
-- #    }

-- # }