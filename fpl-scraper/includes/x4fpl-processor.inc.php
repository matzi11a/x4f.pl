<?php


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
    
//last league: 29946   
//static = "https://fantasy.premierleague.com/drf/bootstrap-static";
//$url = "https://fantasy.premierleague.com/drf/leagues-classic-standings/880";
//$data = $httpRequest->get_http($url);

    public function run() {
        $this->getModels();
        $startTime = microtime(true);
        
        $firstTeamId = $this->runtimeModel->getLastTeamId();
        
        Log::log_message(sprintf("Scanning Leagues from %d", $firstTeamId));
        $lastTeamId = $this->scanFplLeagues($firstTeamId);
        
        Log::log_message("Updating scores");
        $this->updateScores();

        $this->runtimeModel->add($lastTeamId);
        
        Log::log_message(sprintf('Time taken: %01.5f', (microtime(true) - $startTime)));
    }
    
    private function old() {
        
        $data = $this->getData();
        $data = json_decode($data);

        print $data->league->name . "\n";
        print $data->league->id . "\n";

        
        $this->add_team($data);
    }
    
    private function updateScores() {
        foreach ($this->x4TeamModel->getTeams() as $x4Team) {
            if (($data = $this->_fetch($x4Team['team_id'])) != null) {
                foreach ($data->standings->results as $fplteam) {
                    $this->x4PointsModel->save(2018, 10, $fplteam->id, $fplteam->event_total, $fplteam->total);
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

    function getData() {
        $text = <<<EOT
            {
    "new_entries": {
        "has_next": false,
        "number": 1,
        "results": []
    },
    "league": {
        "id": 880,
        "leagueban_set": [
            {
                "entry": 1014542,
                "player_name": "Michael Omotayo"
            },
            {
                "entry": 1013090,
                "player_name": "Michael Babawale omotayo"
            },
            {
                "entry": 503572,
                "player_name": "michael babs"
            },
            {
                "entry": 203648,
                "player_name": "Mikebael Babs"
            },
            {
                "entry": 1013652,
                "player_name": "Babawale Omot"
            }
        ],
        "name": "/r/FantasyPL Telegram",
        "short_name": null,
        "created": "2018-07-05T14:54:52Z",
        "closed": false,
        "forum_disabled": false,
        "make_code_public": false,
        "rank": null,
        "size": null,
        "league_type": "x",
        "_scoring": "c",
        "reprocess_standings": false,
        "admin_entry": 155,
        "start_event": 1
    },
    "standings": {
        "has_next": true,
        "number": 1,
        "results": [
            {
                "id": 10333,
                "entry_name": "Odinforce5000XL",
                "event_total": 50,
                "player_name": "Marwan El-Menawy",
                "movement": "same",
                "own_entry": false,
                "rank": 1,
                "last_rank": 1,
                "rank_sort": 1,
                "total": 636,
                "entry": 413,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 10343,
                "entry_name": "Emeryball",
                "event_total": 48,
                "player_name": "George Pircalabu",
                "movement": "same",
                "own_entry": false,
                "rank": 2,
                "last_rank": 2,
                "rank_sort": 2,
                "total": 619,
                "entry": 1672,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 28761104,
                "entry_name": "Allez allez allez",
                "event_total": 44,
                "player_name": "Dao Hsen Phang",
                "movement": "same",
                "own_entry": false,
                "rank": 3,
                "last_rank": 3,
                "rank_sort": 3,
                "total": 608,
                "entry": 471185,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 10338,
                "entry_name": "Crystal Pulis",
                "event_total": 55,
                "player_name": "Joona Jantunen",
                "movement": "up",
                "own_entry": false,
                "rank": 4,
                "last_rank": 5,
                "rank_sort": 4,
                "total": 604,
                "entry": 361,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 429983,
                "entry_name": "Steady Player Son",
                "event_total": 46,
                "player_name": "Nufail M",
                "movement": "down",
                "own_entry": false,
                "rank": 5,
                "last_rank": 4,
                "rank_sort": 5,
                "total": 602,
                "entry": 91133,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 573044,
                "entry_name": "Real Mati'd",
                "event_total": 63,
                "player_name": "Mati Kettell",
                "movement": "up",
                "own_entry": false,
                "rank": 6,
                "last_rank": 18,
                "rank_sort": 6,
                "total": 587,
                "entry": 119435,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 39281,
                "entry_name": "FekirWrightInDebuchy",
                "event_total": 40,
                "player_name": "\u5b85 \u7537",
                "movement": "down",
                "own_entry": false,
                "rank": 7,
                "last_rank": 6,
                "rank_sort": 7,
                "total": 582,
                "entry": 1515,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 22971,
                "entry_name": "Diego FC",
                "event_total": 43,
                "player_name": "Diego Perez",
                "movement": "up",
                "own_entry": false,
                "rank": 8,
                "last_rank": 9,
                "rank_sort": 8,
                "total": 580,
                "entry": 4948,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 4094379,
                "entry_name": "Sockthatits",
                "event_total": 45,
                "player_name": "Arsene Wonga",
                "movement": "up",
                "own_entry": false,
                "rank": 9,
                "last_rank": 10,
                "rank_sort": 9,
                "total": 580,
                "entry": 12864,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 235624,
                "entry_name": "Umtitty",
                "event_total": 49,
                "player_name": "Alan de Boer",
                "movement": "up",
                "own_entry": false,
                "rank": 10,
                "last_rank": 14,
                "rank_sort": 10,
                "total": 579,
                "entry": 49292,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 153664,
                "entry_name": "Sarri not Sarri",
                "event_total": 42,
                "player_name": "Alexander Harris",
                "movement": "up",
                "own_entry": false,
                "rank": 11,
                "last_rank": 13,
                "rank_sort": 11,
                "total": 574,
                "entry": 33005,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 43831,
                "entry_name": "Tom's Team",
                "event_total": 54,
                "player_name": "Tom Phillpott-Clark",
                "movement": "up",
                "own_entry": false,
                "rank": 12,
                "last_rank": 19,
                "rank_sort": 12,
                "total": 573,
                "entry": 9276,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 29839817,
                "entry_name": "RajniKant\u00e9 \u2734",
                "event_total": 37,
                "player_name": "ROKOVITO PESEYIE",
                "movement": "down",
                "own_entry": false,
                "rank": 13,
                "last_rank": 7,
                "rank_sort": 13,
                "total": 573,
                "entry": 51627,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 1306219,
                "entry_name": "Childish Firmino",
                "event_total": 37,
                "player_name": "Dhaivat Kotecha",
                "movement": "down",
                "own_entry": false,
                "rank": 14,
                "last_rank": 12,
                "rank_sort": 14,
                "total": 570,
                "entry": 271695,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 26474668,
                "entry_name": "Why Always On Top",
                "event_total": 34,
                "player_name": "pierson ang",
                "movement": "down",
                "own_entry": false,
                "rank": 15,
                "last_rank": 8,
                "rank_sort": 15,
                "total": 570,
                "entry": 139959,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 10336,
                "entry_name": "Jason Bournemouth",
                "event_total": 36,
                "player_name": "Yusuf Mukadam",
                "movement": "down",
                "own_entry": false,
                "rank": 16,
                "last_rank": 11,
                "rank_sort": 16,
                "total": 569,
                "entry": 286,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 7930534,
                "entry_name": "NB FC",
                "event_total": 52,
                "player_name": "Nadzri Bakhtiar",
                "movement": "up",
                "own_entry": false,
                "rank": 17,
                "last_rank": 21,
                "rank_sort": 17,
                "total": 569,
                "entry": 184538,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 415301,
                "entry_name": "absolute unit",
                "event_total": 70,
                "player_name": "Matt M",
                "movement": "up",
                "own_entry": false,
                "rank": 18,
                "last_rank": 37,
                "rank_sort": 18,
                "total": 568,
                "entry": 88046,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 10489475,
                "entry_name": "Cech Mek Molek",
                "event_total": 56,
                "player_name": "Oon Persat",
                "movement": "up",
                "own_entry": false,
                "rank": 19,
                "last_rank": 22,
                "rank_sort": 19,
                "total": 568,
                "entry": 451239,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 15875,
                "entry_name": "Original Team Name",
                "event_total": 44,
                "player_name": "Kyle A",
                "movement": "same",
                "own_entry": false,
                "rank": 20,
                "last_rank": 20,
                "rank_sort": 20,
                "total": 565,
                "entry": 3411,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 206282,
                "entry_name": "Standard Joe's",
                "event_total": 56,
                "player_name": "Alan Duffy",
                "movement": "up",
                "own_entry": false,
                "rank": 21,
                "last_rank": 32,
                "rank_sort": 21,
                "total": 562,
                "entry": 43929,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 18416751,
                "entry_name": "t.me/rFantasyPL",
                "event_total": 52,
                "player_name": "Telegram Hivemind",
                "movement": "up",
                "own_entry": false,
                "rank": 22,
                "last_rank": 24,
                "rank_sort": 22,
                "total": 562,
                "entry": 1734,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 1856110,
                "entry_name": "ChittiMaut",
                "event_total": 56,
                "player_name": "Aashish Gaur",
                "movement": "up",
                "own_entry": false,
                "rank": 23,
                "last_rank": 30,
                "rank_sort": 23,
                "total": 560,
                "entry": 341769,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 10335,
                "entry_name": "Marine Saint Patrick",
                "event_total": 61,
                "player_name": "Nicholas Oo",
                "movement": "up",
                "own_entry": false,
                "rank": 24,
                "last_rank": 40,
                "rank_sort": 24,
                "total": 558,
                "entry": 373,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 26029567,
                "entry_name": "Sarri Not Sorry",
                "event_total": 44,
                "player_name": "Vignesh Piramanayagam",
                "movement": "down",
                "own_entry": false,
                "rank": 25,
                "last_rank": 23,
                "rank_sort": 25,
                "total": 558,
                "entry": 2623046,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 2836215,
                "entry_name": "La Honda",
                "event_total": 32,
                "player_name": "Chronus Ess",
                "movement": "down",
                "own_entry": false,
                "rank": 26,
                "last_rank": 16,
                "rank_sort": 26,
                "total": 557,
                "entry": 585189,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 7838103,
                "entry_name": "ApfelSch\u00fcrrle",
                "event_total": 48,
                "player_name": "Grisha Rogov",
                "movement": "same",
                "own_entry": false,
                "rank": 27,
                "last_rank": 27,
                "rank_sort": 27,
                "total": 557,
                "entry": 9389,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 350691,
                "entry_name": "Wooooooow Really ?",
                "event_total": 31,
                "player_name": "Will A",
                "movement": "down",
                "own_entry": false,
                "rank": 28,
                "last_rank": 17,
                "rank_sort": 28,
                "total": 556,
                "entry": 74227,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 877678,
                "entry_name": "Mitbusters",
                "event_total": 54,
                "player_name": "Christopher Fagan",
                "movement": "up",
                "own_entry": false,
                "rank": 29,
                "last_rank": 39,
                "rank_sort": 29,
                "total": 551,
                "entry": 184132,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 55608,
                "entry_name": "ez win boys",
                "event_total": 43,
                "player_name": "Tom Bridger",
                "movement": "down",
                "own_entry": false,
                "rank": 30,
                "last_rank": 28,
                "rank_sort": 30,
                "total": 551,
                "entry": 12001,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 6185965,
                "entry_name": "Spicy Tikka Mosalah",
                "event_total": 55,
                "player_name": "Henry Cox",
                "movement": "up",
                "own_entry": false,
                "rank": 31,
                "last_rank": 44,
                "rank_sort": 31,
                "total": 550,
                "entry": 1206946,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 177152,
                "entry_name": "Scrub central",
                "event_total": 38,
                "player_name": "Amanuel Bayu",
                "movement": "down",
                "own_entry": false,
                "rank": 32,
                "last_rank": 24,
                "rank_sort": 32,
                "total": 548,
                "entry": 37801,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 7880870,
                "entry_name": "Schurrle Winks Atsu",
                "event_total": 39,
                "player_name": "Prajwal Kharel",
                "movement": "down",
                "own_entry": false,
                "rank": 33,
                "last_rank": 29,
                "rank_sort": 33,
                "total": 547,
                "entry": 1520252,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 8359113,
                "entry_name": "No Salah, Mo Mane",
                "event_total": 38,
                "player_name": "Prajwal Kharel",
                "movement": "down",
                "own_entry": false,
                "rank": 34,
                "last_rank": 31,
                "rank_sort": 34,
                "total": 545,
                "entry": 1604239,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 30332198,
                "entry_name": "worldtrash fc",
                "event_total": 40,
                "player_name": "ameer ridhwan",
                "movement": "down",
                "own_entry": false,
                "rank": 35,
                "last_rank": 34,
                "rank_sort": 35,
                "total": 543,
                "entry": 1871284,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 33825209,
                "entry_name": "Pomelo Lukuku",
                "event_total": 36,
                "player_name": "Ryan Chan",
                "movement": "new",
                "own_entry": false,
                "rank": 35,
                "last_rank": 0,
                "rank_sort": 36,
                "total": 543,
                "entry": 4301298,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 25370,
                "entry_name": "TomWiggy",
                "event_total": 34,
                "player_name": "Tom Wiggy",
                "movement": "down",
                "own_entry": false,
                "rank": 37,
                "last_rank": 26,
                "rank_sort": 37,
                "total": 543,
                "entry": 2020,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 1761072,
                "entry_name": "Coen bros.",
                "event_total": 38,
                "player_name": "Dima Griva",
                "movement": "down",
                "own_entry": false,
                "rank": 37,
                "last_rank": 33,
                "rank_sort": 38,
                "total": 543,
                "entry": 1700,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 10340,
                "entry_name": "Dumb & Dummett",
                "event_total": 46,
                "player_name": "Paul Frisby",
                "movement": "up",
                "own_entry": false,
                "rank": 39,
                "last_rank": 42,
                "rank_sort": 39,
                "total": 542,
                "entry": 1257,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 409263,
                "entry_name": "Ant Man",
                "event_total": 49,
                "player_name": "Antony Lau",
                "movement": "up",
                "own_entry": false,
                "rank": 40,
                "last_rank": 46,
                "rank_sort": 40,
                "total": 541,
                "entry": 10167,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 6417492,
                "entry_name": "Guardiolism Era",
                "event_total": 50,
                "player_name": "Stefan Lovekid",
                "movement": "up",
                "own_entry": false,
                "rank": 41,
                "last_rank": 48,
                "rank_sort": 41,
                "total": 541,
                "entry": 383377,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 27806,
                "entry_name": "Leccester",
                "event_total": 57,
                "player_name": "Francesco Isola",
                "movement": "up",
                "own_entry": false,
                "rank": 42,
                "last_rank": 59,
                "rank_sort": 42,
                "total": 541,
                "entry": 1489,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 81748,
                "entry_name": "DarthFc",
                "event_total": 45,
                "player_name": "Aaryak Garg",
                "movement": "down",
                "own_entry": false,
                "rank": 43,
                "last_rank": 38,
                "rank_sort": 43,
                "total": 539,
                "entry": 17941,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 1204319,
                "entry_name": "Magpies",
                "event_total": 41,
                "player_name": "Dotty Elsawi",
                "movement": "down",
                "own_entry": false,
                "rank": 44,
                "last_rank": 41,
                "rank_sort": 44,
                "total": 538,
                "entry": 251447,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 347287,
                "entry_name": "Pep's barbershop",
                "event_total": 51,
                "player_name": "Nik Steele",
                "movement": "up",
                "own_entry": false,
                "rank": 45,
                "last_rank": 61,
                "rank_sort": 45,
                "total": 534,
                "entry": 73493,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 7447304,
                "entry_name": "V\u00e3\u013aa\u0159 M\u00f8\u0159gh\u016f\u013a\u00ees",
                "event_total": 51,
                "player_name": "Kid Shawn",
                "movement": "up",
                "own_entry": false,
                "rank": 46,
                "last_rank": 53,
                "rank_sort": 46,
                "total": 534,
                "entry": 478673,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 11430,
                "entry_name": "Phenoms Knights",
                "event_total": 57,
                "player_name": "Aditya Garg",
                "movement": "up",
                "own_entry": false,
                "rank": 47,
                "last_rank": 63,
                "rank_sort": 47,
                "total": 534,
                "entry": 2478,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 401692,
                "entry_name": "Look the Part FC",
                "event_total": 45,
                "player_name": "Chris Eagles",
                "movement": "up",
                "own_entry": false,
                "rank": 48,
                "last_rank": 55,
                "rank_sort": 48,
                "total": 531,
                "entry": 85143,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 988336,
                "entry_name": "Afifi FC",
                "event_total": 42,
                "player_name": "Youssef Afifi",
                "movement": "up",
                "own_entry": false,
                "rank": 49,
                "last_rank": 50,
                "rank_sort": 49,
                "total": 531,
                "entry": 207797,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            },
            {
                "id": 10331,
                "entry_name": "Pr\u00f6pper Pressure",
                "event_total": 55,
                "player_name": "Mat Williams",
                "movement": "up",
                "own_entry": false,
                "rank": 50,
                "last_rank": 66,
                "rank_sort": 50,
                "total": 530,
                "entry": 48,
                "league": 880,
                "start_event": 1,
                "stop_event": 38
            }
        ]
    },
    "update_status": 0
}
EOT;
        return $text;
    }

}
