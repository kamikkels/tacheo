-- ---
-- Table 'holidays'
-- ---

DROP TABLE IF EXISTS holidays;

CREATE TABLE holidays (
  id INTEGER PRIMARY KEY NOT NULL,
  holiday_name VARCHAR,       -- The name for the public holiday.
  month_Day INTEGER,  -- The month and day of the public holiday. e.g. 1225 for Christmas Day
  year INTEGER,       -- The year if applicable (for use with holidays that differ per year)
  start_time INTEGER, -- If part day holiday start time.
  end_time INTEGER    -- If part day holiday end time.
);

-- ---
-- Table 'locations'
-- ---

DROP TABLE IF EXISTS locations;

CREATE TABLE locations (
  id INTEGER PRIMARY KEY NOT NULL,
  location_name INTEGER NULL DEFAULT NULL   -- Locality name, e.g. Victoria, Australia.
);

-- ---
-- Table 'holiday_locations'
-- ---

DROP TABLE IF EXISTS holiday_locations;

CREATE TABLE holiday_locations (
  id INTEGER PRIMARY KEY NOT NULL,
  holiday_id INTEGER,
  location_id INTEGER,
  FOREIGN KEY(holiday_id) REFERENCES holidays(id),
  FOREIGN KEY(location_id) REFERENCES locations(id)
);

-- ---
-- Test Data
-- ---

-- INSERT INTO holidays (id,holiday_name,month_day,year,start_time,end_time) VALUES
-- (1,'New Years Day',101, NULL, NULL, NULL),
-- (2,'Australia Day',126, NULL, NULL, NULL),
-- (3,'Labour Day', 313, 2017, NULL, NULL),
-- (4,'Labour Day', 312, 2018, NULL, NULL),
-- (5, 'Easter', NULL, NULL, NULL, NULL),
-- (6, 'Eastern Easter', NULL, NULL, NULL, NULL),
-- (7, 'Anzac Day', 425, NULL, NULL, NULL),
-- (8, 'Queens Birthday', 612, 2017, NULL, NULL),
-- (9, 'Queens Birthday', 611, 2018, NULL, NULL),
-- (10, 'Christmas Day', 1225, NULL, NULL, NULL);
-- INSERT INTO holiday_locations (holiday_id,location_id) VALUES
-- (1,1), (1,2), (1,3),
-- (2,1), (2,2), (2,3),
-- (3,2),
-- (4,3),
-- (5,1), (5,2), (5,3),
-- (7,1), (7,2), (7,3),
-- (8,1), (8,2), (8,3),
-- (9,1), (9,2), (9,3),
-- (10,1), (10,2), (10,3);
-- INSERT INTO locations (id,location_name) VALUES
-- (1,'SA, Australia'),
-- (2,'NSW, Australia'),
-- (3,'VIC, Australia');
