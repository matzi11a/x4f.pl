<?php

class RuntimeModel extends Model {
    
    public function add($lastTeamId) {
        Log::log_message('saving runtime ' . $lastTeamId);

        $this->db->run("
                insert into runtime (
                    last_team_id, ts
                ) values (
                   :last_team_id, NOW()
                )
            ", array(
            ':last_team_id' => $lastTeamId
        ));
    }
    
    public function getLastTeamId() {
        return $this->db->get_column('
            select
                last_team_id
            from
                runtime
            order by 
                id DESC
            limit 1
            ', array(
        ));
    }
}