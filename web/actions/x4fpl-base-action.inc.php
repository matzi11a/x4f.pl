<?php
/**
 * Class X4FPLBaseAction
 *
 * @property HaploRouter $router
 * @property HaploConfig $config
 * @property HaploNonce $nonce
 */
abstract class X4FPLBaseAction extends HaploAction {

    /** @var string */
    protected $page;
    /** @var array */
    protected $templateVars = array();
    /** @var string */
    protected $action;
    
    protected $x4TeamModel;
    protected $x4PlayerModel;
    protected $x4RuntimeModel;

    protected function do_init() {
        $this->x4TeamModel = new X4TeamModel();
        $this->x4PlayerModel = new X4PlayerModel();
        $this->x4RuntimeModel = new X4RuntimeModel();
    }

    protected function do_post() {
        $this->action = !empty($_POST['action']) ? $_POST['action'] : null;
        if ($this->action === static::ACTION_FILTER) {
        }
    }

    /**
     * @return bool
     */
    protected function do_post_validate() {
//        if ($this->action === static::ACTION_FILTER) {
//            if (!$this->nonce->check()) {
//                return false;
//            }
            return true;
//        }
//        return false;
    }

    protected function do_post_success() {
        if ($this->action == static::ACTION_FILTER) {
            $this->do_redirect($_SERVER['REQUEST_URI']);
        }
    }

    protected function do_all() {
        $template = new HaploTemplate('_layout.php');
        
        $template->set('currentPage', $_SERVER['REQUEST_URI']);
        //$template->set('page', $this->page);
        
        foreach ($this->templateVars as $key => $value) {
            if ($key == 'metaTitle') {
                $template->set($key, $value.' | X4FPL');
            } else {
                $template->set($key, $value);
            }
        }
        
        error_log("template name : " . $this->templateVars['template']);
        
        $template->set('content', new HaploTemplate($this->templateVars['template']));
        $template->display();
    }
}
