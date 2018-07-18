<?php

namespace TextHyphenation\Database;


class TextHyphenationDatabase extends Database
{
    public function __construct(string $dsn, string $username, string $password)
    {
        parent::__construct($dsn, $username, $password);
        $this->createDatabase('hyphenation');
//        $this->dropTables();
        $this->createTables();
    }

    private function createTables(): void
    {
        $this->createTable('patterns', [
            'id INT(11) AUTO_INCREMENT PRIMARY KEY',
            'pattern VARCHAR(10) UNIQUE NOT NULL'
        ]);
        $this->createTable('words', [
            'id INT(11) AUTO_INCREMENT PRIMARY KEY',
            'word VARCHAR(100) UNIQUE NOT NULL',
            'hyphenated VARCHAR(100) UNIQUE NOT NULL',
        ]);
        $this->createTable('patterns_words', [
            'pattern_id INT(11) NOT NULL',
            'word_id INT(11) NOT NULL',
            'CONSTRAINT pattern_constraint FOREIGN KEY (pattern_id) REFERENCES patterns(id) ON DELETE CASCADE',
            'CONSTRAINT word_constraint FOREIGN KEY (word_id) REFERENCES words(id) ON DELETE CASCADE'
        ]);
    }

    private function dropTables(): void
    {
        $this->dropTable('patterns_words');
        $this->dropTable('patterns');
        $this->dropTable('words');
    }
}