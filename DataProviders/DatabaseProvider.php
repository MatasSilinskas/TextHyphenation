<?php

namespace TextHyphenation\DataProviders;


use PDO;

class DatabaseProvider
{
    private $database;

    public function __construct()
    {
        $this->database = new PDO('mysql:host=localhost', 'root', 'password');
        $this->dropTables();
        $this->createDatabase();
        $this->createTables();
    }

    public function importPatterns(array $patterns)
    {
        $this->database->beginTransaction();
        $this->database->exec('DELETE FROM patterns');
        $stmt = $this->database->prepare('INSERT INTO patterns(pattern) VALUES(?)');
        foreach ($patterns as $pattern) {
            $stmt->bindParam(1, $pattern);
            $stmt->execute();
        }
        $this->database->commit();
    }

    /**
     * @param string $word
     * @param string $hyphenated
     * @return bool
     */
    public function insertWord(string $word, string $hyphenated) : bool
    {
        $stmt = $this->database->prepare('INSERT INTO words(word, hyphenated) VALUES(?, ?)');
        $stmt->bindParam(1, $word);
        $stmt->bindParam(2, $hyphenated);
        return $stmt->execute();
    }

    /**
     * @param string $word
     * @return mixed|null
     */
    public function searchWord(string $word)
    {
        $stmt = $this->database->prepare('SELECT * FROM words WHERE word = ?');
        $stmt->bindParam(1, $word);
        $stmt->execute();
        if ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $result;
        }
        return null;
    }

    private function createTables() : void
    {
        $this->database->exec('CREATE TABLE IF NOT EXISTS patterns(' .
            'id INT(11) AUTO_INCREMENT PRIMARY KEY, ' .
            'pattern VARCHAR(10) UNIQUE NOT NULL)');
        $this->database->exec('CREATE TABLE IF NOT EXISTS words(' .
            'id INT(11) AUTO_INCREMENT PRIMARY KEY, ' .
            'word VARCHAR(100) UNIQUE NOT NULL, ' .
            'hyphenated VARCHAR(100) UNIQUE NOT NULL)');
        $this->database->exec('CREATE TABLE IF NOT EXISTS patterns_words(' .
            'pattern_id INT(11) NOT NULL, ' .
            'word_id INT(11) NOT NULL, ' .
            'FOREIGN KEY (pattern_id) REFERENCES patterns(id), ' .
            'FOREIGN KEY (word_id) REFERENCES words(id))');
    }

    private function createDatabase() : void
    {
        $this->database->exec('CREATE DATABASE IF NOT EXISTS hyphenation');
        $this->database->exec('USE hyphenation');
    }

    private function dropTables() : void
    {
        $this->database->exec('DROP TABLE patterns_words');
        $this->database->exec('DROP TABLE patterns');
        $this->database->exec('DROP TABLE words');
    }
}