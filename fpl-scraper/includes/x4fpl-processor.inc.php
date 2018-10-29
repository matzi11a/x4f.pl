<?php

//https://fantasy.premierleague.com/drf/entry/48/event/10/picks
//https://fantasy.premierleague.com/drf/event/10/live


class X4FplProcessor {

    /** @var X4FplProcessor */
    static protected $instance = null;
    
    protected $x4TeamModel;
    protected $x4PlayerModel;
    protected $x4PointsModel;
    protected $runtimeModel;

    /**
     * @return X4FplProcessor
     */
    static public function get_instance() {
        if (is_null(self::$instance)) {
            self::$instance = new X4FplProcessor;
        }
        return self::$instance;
    }

    /**
     * @throws Exception
     */
    public function __clone() {
        throw new Exception('This class cannot be cloned');
    }

    public function run() {
        $this->getModels();
        $startTime = microtime(true);
        
        //$this->importTeam(880);
        
        $lastRun = $this->runtimeModel->getLastRuntime();        
        $lastTeamId = isset($lastRun['last_team_id']) ? $lastRun['last_team_id'] : 29947;
        
        $gameweek = $this->queryStatic();
        
        Log::log_message(sprintf("Scanning Leagues from %d", $lastTeamId));
        $lastTeamId = $this->scanFplLeagues($lastTeamId);
        
        Log::log_message("Updating scores");
        $this->updateScores($gameweek);

        $this->runtimeModel->add($gameweek, $lastTeamId);
        
        Log::log_message(sprintf('Time taken: %01.5f', (microtime(true) - $startTime)));
    }
    
    private function queryStatic() {
        $httpRequest = HttpRequest::get_instance();
        $data = $httpRequest->get_http("https://fantasy.premierleague.com/drf/bootstrap-static");
        $data = json_decode($data);
        return $data->{'current-event'};
    }
    
    private function updateScores($gameweek) {
        foreach ($this->x4TeamModel->getTeams() as $x4Team) {
            if (($data = $this->_fetch($x4Team['team_id'])) != null) {
                foreach ($data->standings->results as $fplteam) {
                    $this->x4PointsModel->save(2018, $gameweek, $fplteam->id, $fplteam->event_total, $fplteam->total);
                }
            }
        }
    }
    
    private function scanFplLeagues($start) {
        while (($data = $this->_fetch($start)) != null) {            
            Log::log_message(sprintf('found %s', $data->league->name));
            if (substr($data->league->name, 6) === 'X4F.PL') {
                $this->add_team($data);
            }
            $start++;
        }
        return $start;
    }
    
    private function importTeam($id) {
        if (($data = $this->_fetch($id)) != null) {
            Log::log_message(sprintf('fetched %s', $data->league->name));
            $this->add_team($data);
        }
    }

    private function _fetch($id) {
        $httpRequest = HttpRequest::get_instance();
        $data = $httpRequest->get_http("https://fantasy.premierleague.com/drf/leagues-classic-standings/" . $id);
        $data = json_decode($data);
        sleep(3);
        return $data;
    }
    
    private function add_team($data) {
        foreach ($data->standings->results as $fplteam) {
            $this->x4PlayerModel->save($fplteam->id, $data->league->id, $fplteam->entry_name, $fplteam->player_name);
        }
        $this->x4TeamModel->save($data->league->id, $data->league->name);
    }

    function getModels() {
        $this->x4TeamModel = new X4TeamModel();
        $this->x4PlayerModel = new X4PlayerModel();
        $this->x4PointsModel = new X4PointsModel();
        $this->runtimeModel = new RuntimeModel();
    }
}
