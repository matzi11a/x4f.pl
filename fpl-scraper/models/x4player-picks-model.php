<?php

class X4PlayerPicksModel extends Model {
    

    /*
     * Int x4PlayerId
     * Int x4teamId
     * String teamName
     * String playerName
     */
    public function save($gameweek, $x4PlayerId, $position, $premId, $multiplier) {
        Log::log_message('saving picks for ' . $x4PlayerId);

        $this->db->run("
                replace into player_picks (
                    gameweek, player_id, position, prem_id, multiplier
                ) values (
                    :gameweek, :player_id, :position, :prem_id, :multiplier
                )
            ", array(
            ':gameweek' => $gameweek,
            ':player_id' => $x4PlayerId,
            ':position' => $position,
            ':prem_id' => $premId,
            ':multiplier' => $multiplier
        ));

    }
    
    public function has_run($gameweek) {
        $count = $this->db->get_column("
                select count(*) as count from player_picks where gameweek = :gameweek
            ", array(
            ':gameweek' => $gameweek
        ));
        $players = $this->db->get_column("
                select count(*) as count from players
        ");
        return $count = ($players * 15);
    }

}