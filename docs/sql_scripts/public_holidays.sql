-- ---
-- Table 'public_holidays'
-- A list of public holidays
-- ---

DROP TABLE IF EXISTS public_holidays;
		
CREATE TABLE public_holidays (
  id INTEGER PRIMARY KEY NOT NULL,
  name INTEGER, -- The name for the public holiday.
  week INTEGER, -- Either the week in the month or year if no month value is present.
  day_of_week INTEGER, -- Day of week, 0 and 7 are Sunday.
  day_of_month INTEGER, -- Day of month, 1 indexed (1 - 31).
  month INTEGER,  -- Month in year, 1 indexed (1 - 12).
  year INTEGER, -- Year in standard notation, e.g. 1991.
  locality VARCHAR, -- Locality that the holiday applies to, e.g. Victoria, Australia.
  start_time TIME,  -- If part day holiday start time.
  end_time TIME  -- If part day holiday end time.
);

-- ---
-- Test Data
-- ---

-- INSERT INTO public_holidays (name,day_of_month, month, locality) VALUES
-- ('Australia Day','26','1','Australia');
