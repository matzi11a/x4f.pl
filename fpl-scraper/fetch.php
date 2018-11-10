#!/usr/bin/php -q
<?php
require('includes/db.inc.php');
require('includes/model.inc.php');
require('includes/functions.inc.php');
require('includes/http-request.inc.php');
require('includes/log.inc.php');
require('models/x4team-model.php');
require('models/x4player-model.php');
require('models/x4points-model.php');
require('models/runtime-model.php');
require('models/live-points-model.php');
require('models/x4player-picks-model.php');
require('models/hits-model.php');
require('models/winners-model.php');
require('includes/x4fpl-processor.inc.php');
require('config.inc.php');

Log::set_log_type(X4FPL_LOG_TYPE);

$x4Processor = X4FplProcessor::get_instance();

Log::log_message('Starting...');
Log::log_message(sprintf('Run type - %s...', X4FPL_RUN_TYPE));


$x4Processor->run();