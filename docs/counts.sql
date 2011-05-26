
-- SELECT COUNT(1) AS lex FROM lexeme
-- UNION
-- SELECT ((SELECT COUNT(1) AS lex FROM lexeme)/(SELECT COUNT(1) AS tcv FROM table_concat_view)::real)*100;

SELECT 0.2::real/(CAST(NULL AS integer) + 0.3)

