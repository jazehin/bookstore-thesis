import os

# remove file if already exists
if os.path.exists("/home/jazehin/Documents/Programming projects/thesis/miscellaneous/database/dump.sql"):
  os.remove("/home/jazehin/Documents/Programming projects/thesis/miscellaneous/database/dump.sql")

# open write connection to a dump file
dump = open("/home/jazehin/Documents/Programming projects/thesis/miscellaneous/database/dump.sql", "w")

file_names = ["db_create", "konyvadatok_table_create", "felhasznaloadatok_table_create", "rendelesadatok_table_create"]

# write contents of sql files into the dump
for file_name in file_names:
    file = open(f"/home/jazehin/Documents/Programming projects/thesis/miscellaneous/database/{file_name}.sql", "r")
    for x in file:
        dump.write(x)
    file.close()

dump.close()

print("dump done")