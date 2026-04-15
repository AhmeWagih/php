<?php

class Database
{
    private string $host;
    private string $username;
    private string $password;
    private string $databaseName;
    private ?mysqli $connection = null;

    public function __construct(string $host, string $username, string $password, string $databaseName)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->databaseName = $databaseName;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getDatabaseName(): string
    {
        return $this->databaseName;
    }

    public function setDatabaseName(string $databaseName): self
    {
        $this->databaseName = $databaseName;

        return $this;
    }

    public function getConnection(): mysqli
    {
        if ($this->connection instanceof mysqli) {
            return $this->connection;
        }

        $this->connection = new mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->databaseName
        );

        if ($this->connection->connect_error) {
            die('Connection failed: ' . $this->connection->connect_error);
        }

        return $this->connection;
    }
}
