<?php

class X4TeamModel extends HaploModel {
    
    
    /*
     * Int id
     * String name
     */
    public function save($x4TeamId, $name) {
        Log::log_message('saving team ' . $name);

        $this->db->run("
                replace into teams (
                    team_id, team_name, ts
                ) values (
                    :team_id, :team_name, NOW()
                )
            ", array(
            ':team_id' => $x4TeamId,
            ':team_name' => $name
        ));
    }
    
    public function getTeams() {
        return $this->db->get_array('
            select
                team_id, team_name
            from
                teams
            ', array(
        ));
    }
    
    public function getTeamPoints($seasonId, $gameweek) {
        return $this->db->get_array('
            select
                teams.team_id, REPLACE(teams.team_name, " | X4F.PL", "") as team_name, 
                sum(points.event_total) as event_total, sum(points.overall_total) as overall_total, 
                COALESCE(sum(hits.hits_total * -1), 0) as hits, sum(points.event_total) + COALESCE(sum(hits.hits_total * -1), 0) as real_total
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
            left join 
                hits
            on
                hits.season_id = :season_id and hits.gameweek = :gameweek and players.player_id = hits.player_id
            where
                points.season_id = :season_id and
                points.gameweek = :gameweek
            group by
                teams.team_id, teams.team_name
            order by
                real_total DESC
            ', array(
                ':season_id' => 2018,
                ':gameweek' => $gameweek
        ));
    }

    public function getLeaderboard($seasonId, $gameweek) {
        return $this->db->get_array('
                select    
                    teams.team_id, REPLACE(teams.team_name, " | X4F.PL", "") as team_name, 
                    COALESCE(w.wins, 0) as wins, sum(points.overall_total) as overall_total 
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
                left join     
                    (
                    select 
                        team_id, count(distinct(gameweek)) as wins
                    from
                        winners
                    group by 
                        team_id
                    ) w 
                on 
                    w.team_id = teams.team_id
                where 
                    points.season_id = :season_id and points.gameweek = :gameweek
                group by      
                    teams.team_id, teams.team_name, w.wins
                order by 
                    wins DESC, overall_total DESC
            ', array(
                ':season_id' => $seasonId,
                ':gameweek' => $gameweek
        ));
    }
}
