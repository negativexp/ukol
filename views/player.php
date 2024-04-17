<?php
$player = null;
if(isset($playerId)) {
    include_once("db.php");
    $db = new Database();
    $player = $db->getPlayer($playerId);
    if(!$player) header("location: /");
    $stats = $db->getPlayerStats($player);
} else header("location: /");
?>
<!doctype html>
<html lang="cz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../script.js"></script>
    <title>Matyáš Schuller</title>
</head>
<body>
<header>
    <h1>Výsledky stolního tenisu hráče - <?= "{$player["firstName"]} {$player["lastName"]}" ?></h1>
</header>
<nav>
    <a onclick="openPlayerForm()">Přidat hráče</a>
    <a onclick="openMatchForm()">Přidat zápas</a>
    <a href="/" ">Zpátky</a>
</nav>
<main>
    <div class="statistics">
        <div class="statblock">
            <h2>Zápasy</h2>
            <canvas id="pieChart"></canvas>
            <script>
                var ctx = document.getElementById('pieChart').getContext('2d')
                var data = {
                    labels: ["Prohrané", "Vyhrané", "Remíza"],
                    datasets: [{
                        data: [<?=$stats["lostMatches"]?>, <?=$stats["wonMatches"]?>, <?=$stats["tieMatches"]?>],
                        backgroundColor: [
                            'red',
                            'green',
                            'white'
                        ]
                    }]
                }
                new Chart(ctx, {
                    type: 'pie',
                    data: data,
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                labels: {
                                    color: 'black'
                                }
                            }
                        }
                    }
                })
            </script>
        </div>
        <div class="statblock">
            <h2>Sety</h2>
            <canvas id="pieChart2"></canvas>
            <script>
                var ctx2 = document.getElementById('pieChart2').getContext('2d')
                var data = {
                    labels: ["Prohrané", "Vyhrané"],
                    datasets: [{
                        data: [<?=$stats["lostSets"]?>, <?=$stats["wonSets"]?>],
                        backgroundColor: [
                            'red',
                            'green'
                        ]
                    }]
                }
                new Chart(ctx2, {
                    type: 'pie',
                    data: data,
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                labels: {
                                    color: 'black'
                                }
                            }
                        }
                    }
                })
            </script>
        </div>
    </div>
</main>

<div id="playerForm">
    <div id="playerFormheader"><h2>Přidat hráče</h2><a onclick="closePlayerForm()">Zavřít</a></div>
    <form method="post" action="/addPlayer">
        <label>
            <span>Jméno:</span>
            <input type="text" name="firstName" required>
        </label>
        <label>
            <span>Příjmení:</span>
            <input type="text" name="lastName" required>
        </label>
        <input type="submit">
    </form>
</div>
<div id="matchForm">
    <div id="matchFormheader"><h2>Přidat zápas</h2><a onclick="closeMatchForm()">Zavřít</a></div>
    <form method="post" action="/addMatch">
        <label>
            <span>Hráč #1</span>
            <select name="playerOne" required>
                <?php
                $sql = "select * from players";
                $players = $db->fetchRows($db->executeQuery($sql));
                foreach($players as $player) {
                    echo "<option value='{$player["id"]}'>{$player["firstName"]} {$player["lastName"]}</option>";
                }
                ?>
            </select>
        </label>
        <label>
            <span>Hráč #2</span>
            <select name="playerTwo" required>
                <?php
                $sql = "select * from players";
                $players = $db->fetchRows($db->executeQuery($sql));
                foreach($players as $player) {
                    echo "<option value='{$player["id"]}'>{$player["firstName"]} {$player["lastName"]}</option>";
                }
                ?>
            </select>
        </label>
        <label>
            <span>Skóre hráče #1</span>
            <select name="playerOneScore" required>
                <option>1</option>
                <option>2</option>
                <option>3</option>
            </select>
        </label>
        <label>
            <span>Skóre hráče #2</span>
            <select name="playerTwoScore" required>
                <option>1</option>
                <option>2</option>
                <option>3</option>
            </select>
        </label>
        <label>
            <span>Datum a čas odehrání</span>
            <input type="datetime-local" name="datetime" required>
        </label>
        <input type="submit">
    </form>
</div>

</body>
</html>
