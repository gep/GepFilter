CREATE TABLE concat_view (word1 text, ndoc1 BIGINT, nentry1 BIGINT, is_spam SMALLINT, PRIMARY KEY(word1, is_spam));
CREATE TABLE example (id BIGSERIAL, content TEXT, state VARCHAR(255) DEFAULT '1' NOT NULL, created_at TIMESTAMP NOT NULL, PRIMARY KEY(id));
CREATE TABLE ham_stat_view (word text, ndoc BIGINT, nentry BIGINT, PRIMARY KEY(word));
CREATE TABLE knowledge_base (ngram VARCHAR(10), belongs VARCHAR(10), repite BIGINT NOT NULL, percent FLOAT NOT NULL, created_at TIMESTAMP NOT NULL, PRIMARY KEY(ngram, belongs));
CREATE TABLE lexeme (lexeme_item VARCHAR(10), belongs VARCHAR(255), repite BIGINT NOT NULL, percent FLOAT NOT NULL, created_at TIMESTAMP NOT NULL, updated_at TIMESTAMP NOT NULL, PRIMARY KEY(lexeme_item, belongs));
CREATE TABLE spam_stat_view (word text, ndoc BIGINT, nentry BIGINT, PRIMARY KEY(word));
CREATE INDEX viewer_viewed ON knowledge_base (repite);
CREATE INDEX viewer_viewed_index ON lexeme (repite);
