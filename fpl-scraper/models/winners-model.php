<?php

class WinnersModel extends Model {

    public function save($seasonId, $gameweek) {
        Log::log_message('update winners for gameweek ' . $gameweek);
        
        $this->db->run("
            replace into winners
                (season_id, gameweek, team_id)
            select
                points.season_id, points.gameweek, teams.team_id
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
                points.season_id = :season_id and points.gameweek = :gameweek
            group by
                points.season_id, points.gameweek, teams.team_id
            order by
                sum(points.event_total) DESC
            limit 1

        ", array(
                ':season_id' => $seasonId,
                ':gameweek' => $gameweek
        ));    
    }
    
    public function get($seasonId, $gameweek) {
        Log::log_message('get winners for gameweek ' . $gameweek);
        
        return $this->db->get_array('
            select
                team_id
            from
                winners
            where
                season_id = :season_id and
                gameweek = :gameweek
            ', array(
                ':season_id' => $seasonId,
                ':gameweek' => $gameweek
        ));
        
    }
    
    public function hasWinner($seasonId, $gameweek) {
        return $this->db->get_column('
            select
                team_id
            from
                winners
            where
                season_id = :season_id and
                gameweek = :gameweek
            ', array(
                ':season_id' => $seasonId,
                ':gameweek' => $gameweek
        )) > 0;
    }
    
    
}
