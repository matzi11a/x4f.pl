<?php

class RuntimeModel extends Model {
    
    public function add($gameweek, $lastTeamId) {
        Log::log_message('saving runtime ' . $lastTeamId);

        $this->db->run("
                insert into runtime (
                    gameweek, last_team_id, ts
                ) values (
                   :gameweek, :last_team_id, NOW()
                )
            ", array(
            ':gameweek' => $gameweek,    
            ':last_team_id' => $lastTeamId
        ));
    }
    
    public function getLastRuntime() {
        return $this->db->get_row('
            select
                last_team_id, gameweek
            from
                runtime
            order by 
                id DESC
            limit 1
        ');
    }
}