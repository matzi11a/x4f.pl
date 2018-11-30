<?php

class X4RuntimeModel extends HaploModel {
    
    public function getGameweek() {
        return $this->db->get_column('
            select 
                max(gameweek)
            from 
                runtime
        ');
    }
    

}