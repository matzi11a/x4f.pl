create database x4fpl;
create table players (id int NOT NULL AUTO_INCREMENT, team_id int, player_id int, player_team_name varchar(50), player_name varchar(255), ts timestamp, PRIMARY KEY(id), index(team_id), index(player_id));
create table teams (id int NOT NULL AUTO_INCREMENT, team_id int, team_name varchar(100), ts timestamp, PRIMARY KEY(id), index(team_id));
create table points (id int NOT NULL AUTO_INCREMENT, season_id int, gameweek int, player_id int, event_total int, overall_total int, ts timestamp, PRIMARY KEY(id), index(player_id));
create table runtime (id int NOT NULL AUTO_INCREMENT, last_team_id int, ts timestamp, PRIMARY KEY(id));

