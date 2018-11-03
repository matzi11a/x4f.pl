create database x4fpl;
create table players (id int NOT NULL AUTO_INCREMENT, team_id int, player_id int, player_team_name varchar(50), player_name varchar(255), ts timestamp, PRIMARY KEY(id), index(team_id), index(player_id), unique key player_idx (team_id, player_id));
create table teams (id int NOT NULL AUTO_INCREMENT, team_id int, team_name varchar(100), ts timestamp, PRIMARY KEY(id), unique key(team_id));
create table points (id int NOT NULL AUTO_INCREMENT, season_id int, gameweek int, player_id int, event_total int, overall_total int, ts timestamp, PRIMARY KEY(id), index(player_id), unique key player_idx (season_id, gameweek, player_id));
create table runtime (id int NOT NULL AUTO_INCREMENT, gameweek int, last_team_id int, ts timestamp, PRIMARY KEY(id));
create table live (id int NOT NULL AUTO_INCREMENT, gameweek int, prem_id int, points int, index(prem_id), unique key(gameweek, prem_id), primary key (id));
create table player_picks (id int NOT NULL AUTO_INCREMENT, gameweek int, player_id int, position int, prem_id int, multiplier int, index(player_id), unique key(gameweek, player_id, position), primary key (id));