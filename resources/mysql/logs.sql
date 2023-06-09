-- #!mysql

-- # {logs
-- #    {sell
-- #        :player string
-- #        :items string
-- #        :unix int
INSERT INTO Sell (player, items, unix) VALUES (:player, :items, :unix);
-- #    }

-- #    {death_restore
-- #        :id int
SELECT * FROM Deaths WHERE id=:id;
-- #    }
-- # }