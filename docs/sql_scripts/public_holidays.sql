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
-- Table 'locality'
-- ---

DROP TABLE IF EXISTS locality;

CREATE TABLE locality (
  id INTEGER PRIMARY KEY NOT NULL,
  locality VARCHAR NULL DEFAULT NULL   -- Locality name, e.g. Victoria, Australia.
);

-- ---
-- Table 'holiday_localities'
-- ---

DROP TABLE IF EXISTS holiday_localities;

CREATE TABLE holiday_localities (
  id INTEGER PRIMARY KEY NOT NULL,
  holiday_id INTEGER,
  locality_id INTEGER,
  FOREIGN KEY(holiday_id) REFERENCES holidays(id),
  FOREIGN KEY(locality_id) REFERENCES locality(id)
);
