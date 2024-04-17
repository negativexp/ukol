<!doctype html>
<html lang="cz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
    <script defer src="script.js"></script>
    <title>Matyáš Schuller</title>
</head>
<body>
<header>
    <h1>Výsledky stolního tenisu</h1>
</header>
<nav>
    <a onclick="openPlayerForm()">Přidat hráče</a>
    <a onclick="openMatchForm()">Přidat zápas</a>
</nav>
<main>
    <div class="block">
        <h2>Hráči</h2>
        <?php
            include_once("db.php");
            $db = new Database();
            $sql = "select * from players order by id desc";
            $players = $db->fetchRows($db->executeQuery($sql));

            if(count($players) > 0) {
                echo "<table>";
                echo "<thead>";
                echo "<tr>";
                echo "<th>Jméno</th>";
                echo "<th>Příjmení</th>";
                echo "<th>Možnosti</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                foreach($players as $player) {
                    echo "<tr>";
                    echo "<td>{$player["firstName"]}</td>";
                    echo "<td>{$player["lastName"]}</td>";
                    echo "<td><a href='/hrac/{$player["id"]}'>Zobrazit výsledky</a><a href='/deletePlayer?id={$player["id"]}'>Smazat</a></td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
            }
        ?>
    </div>
    <div class="block">
        <h2>Nejnovější zápasy</h2>
        <?php
        $sql = "select * from matches order by id desc";
        $matches = $db->fetchRows($db->executeQuery($sql));

        if(count($matches) > 0) {
            echo "<table>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Jméno</th>";
            echo "<th>Výsledek</th>";
            echo "<th>Datum</th>";
            echo "<th>Možnosti</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            foreach($matches as $match) {
                $sql = "select * from players where id = ?";
                $params = [$match["playerOne"]];
                $playerOne = $db->fetchSingleRow($db->executeQuery($sql, $params));
                $sql = "select * from players where id = ?";
                $params = [$match["playerTwo"]];
                $playerTwo = $db->fetchSingleRow($db->executeQuery($sql, $params));
                $playerOneName =  "(smazanej)";
                $playerTwoName =  "(smazanej)";
                if($playerOne) {
                    $playerOneName =  "{$playerOne["firstName"]} {$playerOne["lastName"]}";
                }
                if($playerTwo) {
                    $playerTwoName = "{$playerTwo["firstName"]} {$playerTwo["lastName"]}";
                }
                echo "<tr>";
                echo "<td>{$playerOneName} Vs. {$playerTwoName}</td>";
                echo "<td>({$match["playerOneScore"]} : {$match["playerTwoScore"]})</td>";
                echo "<td>".date("H:i:s / d.m. Y", strtotime($match["datetime"]))."</td>";
                echo "<td><a href='/deleteMatch?id={$match["id"]}'>Smazat</a></td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
        }
        ?>
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