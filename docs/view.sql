CREATE OR REPLACE VIEW spam_stats AS 
 SELECT ts_stat.word, ts_stat.ndoc, ts_stat.nentry
   FROM ts_stat('SELECT to_tsvector(''russian'', content) FROM example WHERE state = ''spam'''::text) ts_stat(word, ndoc, nentry)
 LIMIT 500;
