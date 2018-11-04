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

    public function updateFromLive($seasonId, $gameweek) {
        Log::log_message('update points from live ' . $gameweek);
        
        $this->db->run("
            replace into points (
                season_id, gameweek, player_id, event_total
            ) select 
                :season_id, live.gameweek, players.player_id, sum(live.points * player_picks.multiplier) as event_total 
            from 
                players 
            left join 
                player_picks 
            on 
                players.player_id = player_picks.player_id 
            left join 
                live 
            on 
                player_picks.prem_id = live.prem_id 
            where 
                live.gameweek = :gameweek
            and
                player_picks.position <= 11
            group by 
                players.player_id
            ", array(
                ':season_id' => $seasonId,
                ':gameweek' => $gameweek
        ));
    }

}
