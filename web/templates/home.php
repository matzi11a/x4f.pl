<?php ?>

<div class="container">
    <div class="row">
        <div class="col-md-6">
            <div class="teamtable">
                <h2>Gameweek <?= $gameweek ?></h2>
                <table>
                    <thead>
                        <tr>
                            <td>#</td>
                            <td>Team</td>
                            <td>Score</td>
                            <td>Hits</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $ii = 0; ?>
                        <?php foreach ($x4teams as $team): ?>
                            <tr>
                                <td><?= ++$ii; ?></td>
                                <td>
                                    <span class="spnDetails"><?= $team['team_name']; ?></span>
                                    <span class="spnTooltip">
                                        <table>
                                        <?php foreach ($x4players[$team['team_id']] as $player): ?>
                                            <tr>
                                            <td><?= $player['player_team_name']; ?></td>
                                            <td><?= $player['event_total']; ?></td>
                                            <td><?= $player['hits']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </table>
                                    </span>
                                </td>
                                <td><?= $team['event_total']; ?></td>
                                <td><?= $team['hits']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <h2>Leaderboard</h2>
            <div>
                <table>
                    <thead>
                        <tr>
                            <td>#</td>
                            <td>Team</td>
                            <td>Wins</td>
                            <td>Points</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $ii = 0; ?>
                        <?php foreach ($leaderboard as $team): ?>
                            <tr>
                                <td><?= ++$ii; ?></td>
                                <td><?= $team['team_name']; ?></td>
                                <td><?= $team['wins']; ?></td>
                                <td><?= $team['overall_total']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <h3>How to join</h3>
            <span class="align-middle">
                <ol>
                    <li>create a league on fpl named for example "My Team Name | X4F.PL"</li>
                    <li>invite 3 buddies to join this "x4 league"</li>
                    <li>this x4 league will be automatically picked up by the x4 scanner at next gameweek</li>
                    <li>scoring, the x4 team has a weekly score (fpl gameweeks) which is the sum of the 4 players scores in the x4 league</li>
                    <li>leaderboard will be ordered number of gameweek wins, then total score (sum of overall score of all four players)</li>
                    <li>later it will be possible to create & join x4 mini leagues ideally accessible via domains like fantasyfootballscout.x4f.pl</li>
                </ol>
            </span>
        </div>
        <div class="col-md-1"></div>
    </div>
</div>   
