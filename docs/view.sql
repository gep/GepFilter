-- CREATE OR REPLACE VIEW spam_stats AS 
--  SELECT ts_stat.word, ts_stat.ndoc, ts_stat.nentry
--    FROM ts_stat('SELECT to_tsvector(''russian'', content) FROM example WHERE state = ''spam'''::text) ts_stat(word, ndoc, nentry)
--  LIMIT 500;

 -- SELECT sum(nentry) as summa
--  FROM spam_stats 
--  GROUP BY word
--  LIMIT 500;
-- 

--SELECT * FROM spam_stat_view LIMIT 1000;

 -- CREATE OR REPLACE VIEW concat_view AS 
--  SELECT word AS word1, ndoc AS ndoc1, nentry AS nentry1, 1 AS is_spam FROM spam_stat_view
-- 
--  UNION
-- 
--  SELECT word AS word1, ndoc AS ndoc1, nentry AS nentry1, 0 AS is_spam FROM ham_stat_view;
-- 
--  CREATE INDEX word1_index ON concat_view (word1);
--  CREATE UNIQUE INDEX word1_is_spam_unique ON concat_view (word1, is_spam);

--SELECT * INTO table_concat_view1 FROM concat_view;


--    INSERT INTO table_concat_view 
--    (
--       SELECT word1,
--              ndoc1,
--              nentry1,
--              is_spam
--         FROM concat_view
--    );
-- 
-- 
--    TRUNCATE TABLE table_concat_view;

SELECT * FROM ts_stat(SELECT to_tsvector('russian', 'лор фывло двло'))
--SELECT to_tsvector('russian', 'лор фывло двло');

--SELECT * FROM concat_views WHERE word1 = 'registr';

-- INSERT INTO 
-- lexeme(lexeme_item, belongs, repite, percent, created_at, updated_at) 
-- VALUES ('registr', 
-- 	'spam', 
-- 	10, 
-- 	(10::float/510103)/((SELECT nentry1::real/71659 FROM concat_view WHERE is_spam = 0 AND word1 = 'registr') + (10::float/510103)), 
-- 	'2011-05-26', 
-- 	'2011-05-26')

--SELECT (10::float/510103)/((SELECT nentry1::real/71659 FROM concat_view WHERE is_spam = 0 AND word1 = 'registr') + (10::float/510103))

--SELECT (10::float/510103)
