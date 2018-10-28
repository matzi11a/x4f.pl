<?php

class X4TeamModel extends Model {
    
    
    /*
     * Int id
     * String name
     */
    public function save($x4TeamId, $name) {
        Log::log_message('saving team ' . $name);

        $this->db->run("
                replace into teams (
                    team_id, team_name, ts
                ) values (
                    :team_id, :team_name, NOW()
                )
            ", array(
            ':team_id' => $x4TeamId,
            ':team_name' => $name
        ));
    }
    
    public function getTeams() {
        return $this->db->get_array('
            select
                team_id, team_name
            from
                teams
            ', array(
        ));
    }

}
