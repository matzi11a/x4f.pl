<?php
class StaticPage extends X4FPLBaseAction {
    protected $template;
    protected $page;

    protected function do_init() {
        if ($this->template = $this->router->get_request_var('template', 'welcome')) {
            $this->template = trim($this->template, '/');
            $this->page = $this->template;

            if (in_array($this->template, $this->exclusions)) {
                $this->do_404();
            }

            if (file_exists(sprintf('%s/%s.php', $this->config->get_key('paths', 'templates'), $this->template))) {
                parent::do_init();
            } else {
                $this->do_404();
            }
        } else {
            throw new HaploException('Static page template not specified in '.$this->router->get_action().'.');
        }
    }

    protected function do_all() {
        $this->templateVars['template'] = "$this->template.php";

        parent::do_all();
    }
}
