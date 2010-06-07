CREATE TABLE example (id BIGSERIAL, content TEXT, state VARCHAR(255) DEFAULT '1' NOT NULL, created_at TIMESTAMP NOT NULL, PRIMARY KEY(id));
CREATE TABLE knowledge_base (ngram VARCHAR(10), belongs VARCHAR(10), repite BIGINT NOT NULL, percent FLOAT NOT NULL, created_at TIMESTAMP NOT NULL, PRIMARY KEY(ngram, belongs));
CREATE INDEX viewer_viewed ON knowledge_base (repite);
