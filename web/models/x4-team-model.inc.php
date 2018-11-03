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
                teams.team_id, REPLACE(teams.team_name, " | X4F.PL", "") as team_name, sum(points.event_total) as event_total, sum(points.overall_total) as overall_total
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
                season_id = :season_id and
                gameweek = :gameweek
            group by
                teams.team_id, teams.team_name
            order by
                event_total DESC
            ', array(
                ':season_id' => 2018,
                ':gameweek' => $gameweek
        ));
    }

}
