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
                        </tr>
                    </thead>
                    <tbody>
                        <?php $ii = 0; ?>
                        <?php foreach ($x4teams as $team): ?>
                            <tr>
                                <td><?= ++$ii; ?></td>
                                <td><?= $team['team_name']; ?></td>
                                <td><?= $team['event_total']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <h2>Leaderboard</h2>
            <div>
                <p>First Round next Gameweek</p>
            </div>
        </div>
    </div>
</div>   
