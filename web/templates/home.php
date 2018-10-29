<?php
?>
<div class="section">
    <h1>X4F.PL</h1>
    <?php foreach ($x4teams as $team): ?>
        <?= $team['team_name']; ?> <?= $team['event_total']; ?>
        <br/>
    <?php endforeach; ?>
</div>