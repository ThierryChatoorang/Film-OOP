<?php
// ============================================
// DATABASE CLASS
// ============================================
class Database {
    private $host = "localhost";
    private $db_name = "film_database";
    private $username = "root";
    private $password = "";
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $e) {
            echo "Connection error: " . $e->getMessage();
        }

        return $this->conn;
    }
}

// ============================================
// FILM CLASS
// ============================================
class Film {
    private $conn;
    private $table_name = "films";

    public $id;
    public $titel;
    public $genre;
    public $jaar;
    public $beschrijving;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Film toevoegen
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET titel=:titel, genre=:genre, jaar=:jaar, beschrijving=:beschrijving";

        $stmt = $this->conn->prepare($query);

        $this->titel = htmlspecialchars(strip_tags($this->titel));
        $this->genre = htmlspecialchars(strip_tags($this->genre));
        $this->jaar = htmlspecialchars(strip_tags($this->jaar));
        $this->beschrijving = htmlspecialchars(strip_tags($this->beschrijving));

        $stmt->bindParam(":titel", $this->titel);
        $stmt->bindParam(":genre", $this->genre);
        $stmt->bindParam(":jaar", $this->jaar);
        $stmt->bindParam(":beschrijving", $this->beschrijving);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Alle films ophalen
    public function readAll() {
        $query = "SELECT id, titel, genre, jaar, beschrijving 
                  FROM " . $this->table_name . " 
                  ORDER BY titel ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Tel aantal films
    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}

// ============================================
// ACTEUR CLASS
// ============================================
class Acteur {
    private $conn;
    private $table_name = "acteurs";

    public $id;
    public $naam;
    public $geboortedatum;
    public $nationaliteit;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Acteur toevoegen
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET naam=:naam, geboortedatum=:geboortedatum, nationaliteit=:nationaliteit";

        $stmt = $this->conn->prepare($query);

        $this->naam = htmlspecialchars(strip_tags($this->naam));
        $this->geboortedatum = htmlspecialchars(strip_tags($this->geboortedatum));
        $this->nationaliteit = htmlspecialchars(strip_tags($this->nationaliteit));

        $stmt->bindParam(":naam", $this->naam);
        $stmt->bindParam(":geboortedatum", $this->geboortedatum);
        $stmt->bindParam(":nationaliteit", $this->nationaliteit);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Alle acteurs ophalen
    public function readAll() {
        $query = "SELECT id, naam, geboortedatum, nationaliteit 
                  FROM " . $this->table_name . " 
                  ORDER BY naam ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Tel aantal acteurs
    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}

// ============================================
// FILM_ACTEUR CLASS
// ============================================
class FilmActeur {
    private $conn;
    private $table_name = "film_acteur";

    public $film_id;
    public $acteur_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Acteur aan film koppelen
    public function koppel() {
        // Check eerst of koppeling al bestaat
        if($this->bestaatKoppeling()) {
            return false;
        }

        $query = "INSERT INTO " . $this->table_name . " 
                  SET film_id=:film_id, acteur_id=:acteur_id";

        $stmt = $this->conn->prepare($query);

        $this->film_id = htmlspecialchars(strip_tags($this->film_id));
        $this->acteur_id = htmlspecialchars(strip_tags($this->acteur_id));

        $stmt->bindParam(":film_id", $this->film_id);
        $stmt->bindParam(":acteur_id", $this->acteur_id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Check of koppeling al bestaat
    public function bestaatKoppeling() {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE film_id = :film_id AND acteur_id = :acteur_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":film_id", $this->film_id);
        $stmt->bindParam(":acteur_id", $this->acteur_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Haal alle koppelingen op voor overzicht
    public function getAlleKoppelingen() {
        $query = "SELECT f.titel, f.jaar, a.naam, a.nationaliteit, fa.film_id, fa.acteur_id
                  FROM " . $this->table_name . " fa
                  INNER JOIN films f ON fa.film_id = f.id
                  INNER JOIN acteurs a ON fa.acteur_id = a.id
                  ORDER BY f.titel, a.naam";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Tel aantal koppelingen
    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?>