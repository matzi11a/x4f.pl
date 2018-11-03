<?php

class LivePointsModel extends Model {
    

    /*
     * Int x4PlayerId
     * Int x4teamId
     * String teamName
     * String playerName
     */
    public function save($gameweek, $premId, $points) {
        Log::log_message('saving live points for ' . $gameweek . " " . $premId . " " . $points);

        $this->db->run("
                replace into live (
                    gameweek, prem_id, points
                ) values (
                    :gameweek, :prem_id, :points
                )
            ", array(
                ':gameweek' => $gameweek,
                ':prem_id' => $premId,
                ':points' => $points
        ));

    }
    

}