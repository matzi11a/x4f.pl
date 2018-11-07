<?php

class HitsModel extends Model {

    public function save($seasonId, $gameweek, $playerId, $hitCost) {
        Log::log_message('update hits from picks ' . $seasonId . " " . $gameweek . " " . $playerId . " " . $hitCost);
        
        $this->db->run("
            replace into hits (
                season_id, gameweek, player_id, hits_total
            ) values (
                :season_id, :gameweek, :player_id, :hit_cost
            )
        ", array(
                ':season_id' => $seasonId,
                ':gameweek' => $gameweek,
                ':player_id' => $playerId,
                ':hit_cost' => $hitCost
        ));    
    }
}
