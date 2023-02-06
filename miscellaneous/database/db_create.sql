DROP DATABASE IF EXISTS konyvadatok;
DROP DATABASE IF EXISTS rendelesadatok;
DROP DATABASE IF EXISTS felhasznaloadatok;

CREATE DATABASE IF NOT EXISTS konyvadatok;
CREATE DATABASE IF NOT EXISTS rendelesadatok;
CREATE DATABASE IF NOT EXISTS felhasznaloadatok;

-- int types size reference: https://dev.mysql.com/doc/refman/5.6/en/integer-types.html