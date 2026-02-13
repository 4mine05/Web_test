<?php

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    private $conexion;

    protected function setUp(): void
    {
        $this->conexion = mysqli_connect(
            getenv('DB_HOST') ?: '127.0.0.1',
            'root',
            'foro_pass',
            'foro',
            3306
        );
    }

    public function testConexionBD()
    {
        $this->assertNot
            False($this->conexion);
    }

    public function testSelectBasico()
    {
        $result = mysqli_query($this->conexion, "SELECT 1");
        $this->assertNotFalse($result);
    }
}
