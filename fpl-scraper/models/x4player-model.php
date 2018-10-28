<?php

class X4PlayerModel extends Model {
    

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
    
    
    

}