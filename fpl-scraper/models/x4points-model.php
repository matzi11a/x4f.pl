<?php

class X4PointsModel extends Model {
    

    public function save($seasonId, $gameweek, $x4PlayerId, $eventTotal, $overallTotal) {
        Log::log_message('saving points ' . $x4PlayerId . ' ' . $eventTotal);

        $this->db->run("
                replace into points (
                    season_id, gameweek, player_id, event_total, overall_total, ts
                ) values (
                    :season_id, :gameweek, :player_id, :event_total, :overall_total, NOW()
                )
            ", array(
            ':season_id' => $seasonId,
            ':gameweek' => $gameweek,
            ':player_id' => $x4PlayerId,
            ':event_total' => $eventTotal,
            ':overall_total' => $overallTotal
        ));
    }
}