-- #!mysql

-- # {bounty.init
CREATE TABLE IF NOT EXISTS Bounty (
    username VARCHAR(20) PRIMARY KEY,
    currentBounty BIGINT,
    lifeTimeBounty BIGINT,
    maxBounty BIGINT,
    earned BIGINT
);
-- #}


-- #{bounty.select
-- #    :username string
SELECT * FROM Bounty WHERE LOWER(username) = LOWER(:username);
-- #}

-- #{bounty.current
SELECT username, currentBounty FROM Bounty ORDER BY currentBounty DESC;
-- #}

-- #{bounty.update
-- #    :username string
-- #    :currentBounty int
-- #    :lifeTimeBounty int
-- #    :maxBounty int
-- #    :earned int
REPLACE INTO Bounty (username, currentBounty, lifeTimeBounty, maxBounty, earned) VALUES (:username, :currentBounty, :lifeTimeBounty, :maxBounty, :earned);
-- #}

-- # {bounty.history.init
CREATE TABLE IF NOT EXISTS BountyHistory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player VARCHAR(20),
    killer VARCHAR(20),
    amount BIGINT,
    unix BIGINT
);
-- #}

-- #{bounty.history.update
-- #    :player string
-- #    :killer string
-- #    :amount int
-- #    :unix int
REPLACE INTO BountyHistory (player, killer, amount, unix)  VALUES (:player, :killer, :amount, :unix);
-- #}