<?php

/**
 * Class Home
 */
class Home extends X4FPLBaseAction {

    /** @var string */
    protected $page = 'welcome';

    /** @var string */
    protected $redirectUrl;

    /** @var string */
    protected $view;

    /** @var string */
    protected $error;

    protected function do_init() {
        parent::do_init();
    }

    protected function do_get() {
        $this->redirectUrl = filter_input(INPUT_GET, 'url');
    }

    protected function do_post() {
        $this->redirectUrl = filter_input(INPUT_POST, 'url');
    }

    protected function do_all() {
        $this->templateVars['page'] = $this->page;
        $this->templateVars['redirectUrl'] = $this->redirectUrl;
        $this->templateVars['error'] = $this->error;
        $this->templateVars['template'] = 'home.php';
        
        
        $gameweek = 13;
        $lastGameweek = 12;
        
        $players = array();
        foreach ($this->x4PlayerModel->getPlayerPoints(2018, $gameweek) as $player) {
            $players[$player['team_id']] []=  $player;
        }
        
        $this->templateVars['x4players'] = $players;
        $this->templateVars['x4teams'] = $this->x4TeamModel->getTeamPoints(2018, $gameweek);
        $this->templateVars['leaderboard'] = $this->x4TeamModel->getLeaderboard(2018, $lastGameweek);
        $this->templateVars['gameweek'] = $gameweek;

        parent::do_all();
    }

}
