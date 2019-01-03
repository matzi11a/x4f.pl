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
    protected $livePointsModel;
    protected $x4playerPicksModel;
    protected $hitsModel;
    protected $winnersModel;

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

    //https://x4f.pl/

    public function run() {
        $this->getModels();
        $startTime = microtime(true);

//        $this->importTeam(1267602);
//        $this->importTeam(328);
//        $this->importTeam(1265830);
//        $this->importTeam(1322524);

        $lastRun = $this->runtimeModel->getLastRuntime();
        $lastTeamId = isset($lastRun['last_team_id']) ? $lastRun['last_team_id'] : 29947;
        
        $gameweek = $this->queryStatic();

        $gameweekCompleted = $this->winnersModel->hasWinner(2018, $gameweek);
        Log::log_message(sprintf("Gameweek complted? %s", ($gameweekCompleted ? 'yes' : 'no')));
                
        if (!$gameweekCompleted) {
            Log::log_message(sprintf("Updating live match scores"));
            $state = $this->updateLive($gameweek);
            Log::log_message(sprintf("Gameweek progress %s", print_r($state, true)));
        }
        
        if ($gameweekCompleted || !$state['in_progress']) {
            Log::log_message(sprintf("Scanning Leagues from %d", $lastTeamId));
            $lastTeamId = $this->scanFplLeagues($lastTeamId);
        }

        //only do this once at the beginning of the gameweek
        if (!$this->x4playerPicksModel->has_run($gameweek)) {
            Log::log_message("Sleeping 15 mins before pulling picks");
            sleep(900);
            Log::log_message(sprintf("Updating player picks"));
            $this->updatePlayerPicks($gameweek);
        } else {
            Log::log_message(sprintf("Updating player picks"));
            if (!$gameweekCompleted) {
                if ($state['all_done']) {
                    Log::log_message("Sleeping 8 hours for updates on fpl to complete");
                    sleep(3600 * 8);
                }
                Log::log_message("Updating scores");
                $finishedGameweek = $this->updateScores($gameweek, $state['all_done']);

                if ($finishedGameweek) {
                    $this->winnersModel->save(2018, $gameweek);
                }
            }
        }

        $this->runtimeModel->add($gameweek, $lastTeamId);

        Log::log_message(sprintf('Time taken: %01.5f', (microtime(true) - $startTime)));
    }

    private function updatePlayerPicks($gameweek) {
        foreach ($this->x4PlayerModel->getPlayers() as $x4Player) {
            if (($data = $this->_fetchPlayerPicks($x4Player['player_id'], $gameweek)) != null) {
                foreach ($data->picks as $pick) {
                    $this->x4playerPicksModel->save($gameweek, $x4Player['player_id'], $pick->position, $pick->element, $pick->multiplier);
                }
                if ($data->entry_history->event_transfers_cost > 0) {
                    $this->hitsModel->save(2018, $gameweek, $x4Player['player_id'], $data->entry_history->event_transfers_cost);
                }
            }
        }
    }

    private function updateLive($gameweek) {
        $httpRequest = HttpRequest::get_instance();
        $data = $httpRequest->get_http("https://fantasy.premierleague.com/drf/event/" . $gameweek . "/live");
        $data = json_decode($data);
        foreach ($data->elements as $premId => $element) {
            //echo($premId . " " . "\n");
            $points = 0;
            foreach ($element->explain as $matchPlayerDetail) {
                //echo(print_r($matchPlayerDetail, true) . " " . "\n");
                foreach ($matchPlayerDetail as $matchPlayerDetailEntry) {
                    //echo(print_r($matchPlayerDetailEntry, true) . " " . "\n");
                    if (is_object($matchPlayerDetailEntry)) {
                        //echo(print_r(get_object_vars($matchPlayerDetailEntry), true) . " " . "\n");
                        foreach (get_object_vars($matchPlayerDetailEntry) as $stat) {
                            //print $stat->name . " " . $stat->points . "\n";
                            $points += $stat->points;
                        }
                        //echo($matchPlayerDetailEntry->minutes->points . " " . "\n");
                    }
                }
            }
            $this->livePointsModel->save($gameweek, $premId, $points);
        }
        $alldone = true;
        $inprogress = false;
        foreach ($data->fixtures as $event) {
            $alldone = $alldone && $event->finished;
            $inprogress = $inprogress || ($event->started && !$event->finished_provisional);
        }
        return array(
            'all_done' => $alldone,
            'in_progress' => $inprogress
        );
    }

    private function queryStatic() {
        $httpRequest = HttpRequest::get_instance();
        $data = $httpRequest->get_http("https://fantasy.premierleague.com/drf/bootstrap-static");
        $data = json_decode($data);
        return $data->{'current-event'};
    }

    private function updateScores($gameweek, $endOfgames) {
        //which one? what time is it?
        $finished = false;
        if ($endOfgames) {
            $finished = $this->updateScoresFinal($gameweek);
        } else {
            $this->updateScoresLive($gameweek);
        }
        return $finished;
    }

    private function updateScoresFinal($gameweek) {
        $count = 0;
        $x4Teams = $this->x4TeamModel->getTeams();
        foreach ($x4Teams as $x4Team) {
            if (($data = $this->_fetch($x4Team['team_id'])) != null) {
                foreach ($data->standings->results as $fplteam) {
                    $this->x4PointsModel->save(2018, $gameweek, $fplteam->entry, $fplteam->event_total, $fplteam->total);
                    if ($fplteam->total > 0) {
                        $count++;
                    }
                }
            }
        }
        return ($count >= (count($x4Teams) * 4) - 2);
    }

    private function updateScoresLive($gameweek) {
        $this->x4PointsModel->updateFromLive(2018, $gameweek);
    }

    private function scanFplLeagues($start) {
        $fails = 0;
        while ($fails < 10) {
            try {
                if (($data = $this->_fetch($start)) != null) {
                    Log::log_message(sprintf('found %s', $data->league->name));
                    if (substr($data->league->name, -6) === 'X4F.PL') {
                        $this->add_team($data);
                    }
                    $fails = 0;
                } else {
                    throw new Exception('Failed fetch: ' . $start);
                }
            } catch (Exception $e) {
                echo 'Caught exception: ', $e->getMessage(), "\n";
                $fails++;
            }
            $start++;
        }
        return $start - $fails;
    }

    private function importTeam($id) {
        if (($data = $this->_fetch($id)) != null) {
            Log::log_message(sprintf('fetched %s', $data->league->name));
            $this->add_team($data);
        }
    }

    private function _fetch($id) {
        sleep(2);                                       //lets be nice to the server
        $httpRequest = HttpRequest::get_instance();
        $data = $httpRequest->get_http("https://fantasy.premierleague.com/drf/leagues-classic-standings/" . $id);
        if ($data !== false) {
            $data = json_decode($data);
        }
        return $data;
    }

    private function _fetchPlayerPicks($playerId, $gameweek) {
        sleep(2);                                       //lets be nice to the server
        $httpRequest = HttpRequest::get_instance();
        $data = $httpRequest->get_http(sprintf("https://fantasy.premierleague.com/drf/entry/%d/event/%d/picks", $playerId, $gameweek));
        if ($data !== false) {
            $data = json_decode($data);
        }
        return $data;
    }

    private function add_team($data) {
        Log::log_message(sprintf('adding team %s', $data->league->name));
        foreach ($data->standings->results as $fplteam) {
            $this->x4PlayerModel->save($fplteam->entry, $data->league->id, $fplteam->entry_name, $fplteam->player_name);
        }
        foreach ($data->new_entries->results as $fplteam) {
            $this->x4PlayerModel->save($fplteam->entry, $data->league->id, $fplteam->entry_name, $fplteam->player_first_name . " " . $fplteam->player_last_name);
        }
        $this->x4TeamModel->save($data->league->id, $data->league->name);
    }

    function getModels() {
        $this->x4TeamModel = new X4TeamModel();
        $this->x4PlayerModel = new X4PlayerModel();
        $this->x4PointsModel = new X4PointsModel();
        $this->runtimeModel = new RuntimeModel();
        $this->livePointsModel = new LivePointsModel();
        $this->x4playerPicksModel = new X4PlayerPicksModel();
        $this->hitsModel = new HitsModel();
        $this->winnersModel = new WinnersModel();
    }

}
