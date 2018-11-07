create database x4fpl;
create table players (id int NOT NULL AUTO_INCREMENT, team_id int, player_id int, player_team_name varchar(50), player_name varchar(255), ts timestamp, PRIMARY KEY(id), index(team_id), index(player_id), unique key player_idx (team_id, player_id));
create table teams (id int NOT NULL AUTO_INCREMENT, team_id int, team_name varchar(100), ts timestamp, PRIMARY KEY(id), unique key(team_id));
create table points (id int NOT NULL AUTO_INCREMENT, season_id int, gameweek int, player_id int, event_total int, overall_total int, ts timestamp, PRIMARY KEY(id), index(player_id), unique key player_idx (season_id, gameweek, player_id));
create table runtime (id int NOT NULL AUTO_INCREMENT, gameweek int, last_team_id int, ts timestamp, PRIMARY KEY(id));
create table live (id int NOT NULL AUTO_INCREMENT, gameweek int, prem_id int, points int, index(prem_id), unique key(gameweek, prem_id), primary key (id));
create table player_picks (id int NOT NULL AUTO_INCREMENT, gameweek int, player_id int, position int, prem_id int, multiplier int, index(player_id), unique key(gameweek, player_id, position), primary key (id));

create table hits (id int NOT NULL AUTO_INCREMENT, season_id int, gameweek int, player_id int, hits_total int, primary key(id), unique key (season_id, gameweek, player_id));

create table winners (id int NOT NULL AUTO_INCREMENT, season_id int, gameweek int, team_id int, PRIMARY KEY(id), unique key player_idx (season_id, gameweek, team_id));



SET @row_number = 0;

replace into winners
    (season_id, gameweek, team_id)
select
                points.season_id, points.gameweek, teams.team_id,  (@row_number:=@row_number + 1)
            from
                teams
            left join
                players
            on
                teams.team_id = players.team_id
            left join 
                points
            on 
                players.player_id = points.player_id
            where
                points.season_id = 2018 and points.gameweek = 11
            group by
                points.season_id, points.gameweek, teams.team_id
            order by
                sum(points.event_total) DESC
            limit 1


