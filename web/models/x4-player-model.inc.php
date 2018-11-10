<?php

class X4PlayerModel extends HaploModel {
    

    /*
     * Int x4PlayerId
     * Int x4teamId
     * String teamName
     * String playerName
     */
    public function save($x4PlayerId, $x4TeamId, $teamName, $playerName) {
        Log::log_message('saving player ' . $teamName);

        $this->db->run("
                replace into players (
                    team_id, player_id, player_team_name, player_name, ts
                ) values (
                    :team_id, :player_id, :player_team_name, :player_name, NOW()
                )
            ", array(
            ':team_id' => $x4TeamId,
            ':player_id' => $x4PlayerId,
            ':player_team_name' => $teamName,
            ':player_name' => $playerName
        ));

    }
    
    public function getPlayerPoints($seasonId, $gameweek) {
        return $this->db->get_array('
            select 
                players.team_id, players.player_team_name, players.player_name, points.event_total, COALESCE((hits.hits_total * -1), 0) as hits  
            from 
                players 
            left join 
                points on players.player_id = points.player_id 
            left join 
                hits on hits.season_id = points.season_id and hits.gameweek = points.gameweek and hits.player_id = players.player_id   
            where 
                points.season_id = :season_id and  points.gameweek = :gameweek
        ', array(
                ':season_id' => $seasonId,
                ':gameweek' => $gameweek
        ));
    }
    

}