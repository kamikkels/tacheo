-- ---
-- Table 'Holidays'
-- ---

DROP TABLE IF EXISTS Holidays;

CREATE TABLE Holidays (
  id INTEGER PRIMARY KEY NOT NULL,
  Name VARCHAR,       -- The name for the public holiday.
  Month_Day INTEGER,  -- The month and day of the public holiday. e.g. 1225 for Christmas Day
  Year INTEGER,       -- The year if applicable (for use with holidays that differ per year)
  Start_Time INTEGER, -- If part day holiday start time.
  End_Time INTEGER    -- If part day holiday end time.
);

-- ---
-- Table 'Locations'
-- ---

DROP TABLE IF EXISTS Locations;

CREATE TABLE Locations (
  id INTEGER PRIMARY KEY NOT NULL,
  Name INTEGER NULL DEFAULT NULL   -- Locality name, e.g. Victoria, Australia.
);

-- ---
-- Table 'Holiday_Locations'
-- ---

DROP TABLE IF EXISTS Holiday_Locations;

CREATE TABLE Holiday_Locations (
  id INTEGER PRIMARY KEY NOT NULL,
  Holiday_id INTEGER,
  Location_id INTEGER,
  FOREIGN KEY(Holiday_id) REFERENCES Holidays(id),
  FOREIGN KEY(Location_id) REFERENCES Locations(id)
);

-- ---
-- Test Data
-- ---

-- INSERT INTO Holidays (id,Name,Day_Month,Year,Start_Time,End_Time) VALUES
-- (1,'Australia Day',126, NULL, NULL, NULL),
-- (2,'Labour Day', 313, 2017, NULL, NULL),
-- (3,'Labour Day', 312, 2018, NULL, NULL),
-- INSERT INTO Holiday_Locations (Holiday_id,Location_id) VALUES
-- (1,1),
-- (1,2),
-- (1,3),
-- (2,3),
-- (2,3),
-- (3,3);
-- INSERT INTO Locations (id,Name) VALUES
-- (1,'SA, Australia'),
-- (2,'NSW, Australia'),
-- (3,'VIC, Australia');
