<?php ?>

<div class="container">
    <div class="row">
        <div class="col-md-6">
            <h2>Gameweek 10</h2>
            <div>
                <?php foreach ($x4teams as $team): ?>
                    <div>
                        <?= $team['team_name']; ?> <?= $team['event_total']; ?>
                    </div>
                    <br/>
                <?php endforeach; ?>
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
