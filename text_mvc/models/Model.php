<?php

class Model
{
    protected $pdo;
    protected $table;

    public function __construct($table)
    {
        $this->table = $table;
        $server_name = $_ENV['DB_SERVER'];
        $db_name = $_ENV['DB_DATABASE'];
        $username = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];

        $dsn = "mysql:host={$server_name};dbname={$db_name}";

        try {
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }

    public function all()
    {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table}"); // Use double quotes
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id"); // Use double quotes
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        // Prepare column names and placeholders
        $keys = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data)); // Adds `:` before each placeholder

        // Prepare the SQL statement with placeholders
        $sql = "INSERT INTO {$this->table} ($keys) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);

        // Execute the statement with the data
        $stmt->execute($data);
    }


    public function update($id, $data)
    {
        $fields = '';
        foreach ($data as $key => $value) {
            $fields .= "{$key} = :{$key}, "; // Use double quotes for clarity
        }
        $fields = rtrim($fields, ', ');
        $sql = "UPDATE {$this->table} SET {$fields} WHERE id = :id"; // Use double quotes
        $stmt = $this->pdo->prepare($sql);
        $data['id'] = $id; // Include ID in the data array
        $stmt->execute($data);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id"); // Use double quotes
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
}
