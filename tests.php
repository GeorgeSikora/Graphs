<div style="display: flex">
        <div style="width: 50%">
            <?=createGraph("http://localhost/graphs/getGraphData.php?from=2000-01-01&to=2099-01-01")?>
        </div>
        <div style="width: 50%">
            <?=createGraph("http://localhost/graphs/getGraphData.php?interval=1month")?>
        </div>
    </div>

    <div style="display: flex">
        <div style="width: 33.33%">
            <?=createGraph("http://localhost/graphs/graphPageVisits.php?interval=1day")?>
        </div>
        <div style="width: 33.33%">
            <?=createGraph("http://localhost/graphs/graphUserRegistrations.php?interval=3month")?>
        </div>
        <div style="width: 33.33%">
            <?=createGraph("http://localhost/graphs/getGraphData.php?interval=2week")?>
        </div>
    </div>