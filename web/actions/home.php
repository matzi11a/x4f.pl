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

        parent::do_all();
    }

}
