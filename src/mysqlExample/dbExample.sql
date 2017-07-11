CREATE DATABASE IF NOT EXISTS casino;

CREATE TABLE IF NOT EXISTS casino.players
(
  id INT(11) NOT NULL AUTO_INCREMENT,
  name VARCHAR(20) NOT NULL,
  email VARCHAR(20) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS casino.balances
(
  id INT(11) NOT NULL AUTO_INCREMENT,
  player_id INT(11) NOT NULL,
  amount DECIMAL(15,2) NOT NULL DEFAULT '0.00',
  currency VARCHAR(4) NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (player_id)
    REFERENCES casino.players(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS casino.transactions
(
  id INT(11) NOT NULL AUTO_INCREMENT,
  player_id INT(11) NOT NULL,
  balance_id INT(11) NOT NULL,
  game_uuid VARCHAR(255) NOT NULL,
  session_id VARCHAR(255) NOT NULL,
  transaction_id VARCHAR(255) NOT NULL,
  action VARCHAR(20) NOT NULL,
  amount DECIMAL(15,2) NOT NULL,
  currency VARCHAR(4) NOT NULL,
  balance DECIMAL(15,2) NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (player_id)
    REFERENCES casino.players(id),
  FOREIGN KEY (balance_id)
    REFERENCES casino.balances(id)
) ENGINE=InnoDB;