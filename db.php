<?php
class Database {
    private $servername = "localhost";
    private $dbusername = "root";
    private $dbpassword = "password";
    private $database = "ukol";
    private $conn;
    public function __construct() {
        $this->connect();
    }
    private function connect() {
        $this->conn = new mysqli($this->servername, $this->dbusername, $this->dbpassword, $this->database);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }
    private function disconnect(): void {
        $this->conn->close();
    }
    public function getLastInsertedId() {
        return $this->conn->insert_id;
    }

    // for non-select queries, set returnResult to false on call.
    public function executeQuery($sql, $params = [], $returnResult = true) {
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die("Error in prepare statement: " . $this->conn->error);
        }
        if (!empty($params)) {
            $types = '';
            $bindParams = [&$types];
            foreach ($params as &$param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } elseif (is_string($param)) {
                    $types .= 's';
                } else {
                    $types .= 'b';
                }
                $bindParams[] = &$param;
            }
            call_user_func_array([$stmt, 'bind_param'], $bindParams);
        }
        $stmt->execute();
        if ($stmt->error) {
            die("Error in execute statement: " . $stmt->error);
        }
        if ($returnResult) {
            $result = $stmt->get_result();
            if ($result === false) {
                die("Error in getting result: " . $stmt->error);
            }
            return $result;
        }

        $success = $stmt->affected_rows > 0;
        $stmt->close();
        return $success;
    }
    public function fetchRows($result) {
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }
    public function fetchSingleRow($result) {
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    public function htmlspecial($value): string {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5);
    }

    public function addPlayer($firstName, $lastName): void {
        $sql = "insert into players (firstName, lastName) values (?, ?)";
        $params = [$firstName, $lastName];
        if($this->executeQuery($sql, $params, false)) {
            header("location: /");
        } else header("location: /?error=addPlayer");
        exit();
    }

    public function deletePlayer($id): void {
        $sql = "delete from players where id = ?";
        $params = [$id];
        if($this->executeQuery($sql, $params, false)) {
            header("location: /");
        } else header("location: /?error=deletePlayer");
        exit();
    }

    public function deleteMatch($id): void {
        $sql = "delete from matches where id = ?";
        $params = [$id];
        if($this->executeQuery($sql, $params, false)) {
            header("location: /");
        } else header("location: /?error=deleteMatch");
        exit();
    }

    public function addMatch($playerOne, $playerTwo, $playerOneScore, $playerTwoScore, $datetime): void {
        $sql = "insert into matches (playerOne, playerTwo, playerOneScore, playerTwoScore, datetime) values (?,?,?,?,?)";
        $params = [$playerOne, $playerTwo, $playerOneScore, $playerTwoScore, $datetime];
        if($playerOne === $playerTwo) {
            header("location: /?error=addMatchSamePerson");
            exit();
        }
        if($playerOneScore > $playerTwoScore) {
            if($playerOneScore != 3) {
                header("location: /?error=addMatchWinnerScore");
                exit();
            }
        }
        if ($playerTwoScore > $playerOneScore) {
            if($playerTwoScore != 3) {
                header("location: /?error=addMatchWinnerScore");
                exit();
            }
        }
        if ($playerTwoScore == 1 && $playerOneScore == 1) {
            header("location: /?error=addMatchWinnerScore");
            exit();
        }
        if($this->executeQuery($sql, $params, false)) {
            header("location: /");
        } else header("location: /?error=addMatch");
        exit();
    }

    public function getPlayer($id) {
        $sql = "select * from players where id = ?";
        $params = [$id];
        $result = $this->executeQuery($sql, $params);
        if($result) {
            return $this->fetchSingleRow($result);
        } else header("location: /?error=getPlayer");
        exit();
    }

    public function getPlayerStats($player) {
        $sql = "SELECT * FROM matches WHERE playerOne = ? OR playerTwo = ?";
        $params = [$player["id"], $player["id"]];
        $matches = $this->fetchRows($this->executeQuery($sql, $params));
        $wonMatches = 0;
        $lostMatches = 0;
        $tieMatches = 0;
        $wonSets = 0;
        $lostSets = 0;
        if(count($matches) == 0) {
            return null;
        } else {
            foreach($matches as $match) {
                if($match["playerOne"] == $player["id"]) {
                    if($match["playerOneScore"] > $match["playerTwoScore"]) $wonMatches++;
                    if($match["playerOneScore"] == $match["playerTwoScore"]) $tieMatches++;
                    if($match["playerOneScore"] < $match["playerTwoScore"]) $lostMatches++;
                    $wonSets += $match["playerOneScore"];
                    // Calculate lost sets
                    $lostSets += $match["playerTwoScore"];
                }
                if($match["playerTwo"] == $player["id"]) {
                    if($match["playerTwoScore"] > $match["playerOneScore"]) $wonMatches++;
                    if($match["playerTwoScore"] == $match["playerOneScore"]) $tieMatches++;
                    if($match["playerTwoScore"] < $match["playerOneScore"]) $lostMatches++;
                    $wonSets += $match["playerTwoScore"];
                    // Calculate lost sets
                    $lostSets += $match["playerOneScore"];
                }
            }
        }
        // Return the calculated stats
        return [
            'wonMatches' => $wonMatches,
            'lostMatches' => $lostMatches,
            'tieMatches' => $tieMatches,
            'wonSets' => $wonSets,
            'lostSets' => $lostSets
        ];
    }
}

$db = new Database();
