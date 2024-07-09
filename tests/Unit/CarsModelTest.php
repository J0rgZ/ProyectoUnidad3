<?php

namespace Tests;

use App\Models\CarsModel;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as M;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../vendor/autoload.php';

class CarsModelTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected $mockQuery;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockQuery = M::mock('overload:App\Models\Query'); // Mockear la clase Query utilizada en CarsModel
    }

    public function tearDown(): void
    {
        parent::tearDown();
        M::close();
    }

    public function testGetVehiculos()
    {
        // Preparar los datos esperados
        $mockedData = [
            ['id' => 1, 'placa' => 'ABC123', 'id_tipo' => 1, 'id_marca' => 1, 'estado' => 1],
            ['id' => 2, 'placa' => 'XYZ789', 'id_tipo' => 2, 'id_marca' => 2, 'estado' => 1],
        ];

        // Estado para filtrar vehículos activos (estado = 1)
        $estado = 1;

        // Configurar el comportamiento esperado del mock de Query para el método selectAll
        $this->mockQuery->shouldReceive('selectAll')
            ->once()
            ->with("SELECT v.*, m.marca, t.tipo FROM vehiculos v INNER JOIN marcas m ON v.id_marca = m.id INNER JOIN tipos t ON v.id_tipo = t.id WHERE v.estado != $estado")
            ->andReturn($mockedData);

        // Instanciar el modelo y llamar al método que queremos probar
        $carsModel = new CarsModel($this->mockQuery);
        $result = $carsModel->getVehiculos($estado);

        // Asegurar que el resultado coincide con los datos esperados
        $this->assertEquals($mockedData, $result);
    }

    public function testGetVehiculo()
    {
        // Preparar los datos esperados
        $mockedData = ['id' => 1, 'placa' => 'ABC123', 'id_tipo' => 1, 'id_marca' => 1, 'estado' => 1];

        // Configurar el comportamiento esperado del mock de Query para el método select
        $this->mockQuery->shouldReceive('select')
            ->once()
            ->andReturn($mockedData);

        // ID del vehículo a consultar
        $vehiculoId = 1;

        // Instanciar el modelo y llamar al método que queremos probar
        $carsModel = new CarsModel($this->mockQuery);
        $result = $carsModel->getVehiculo($vehiculoId);

        // Asegurar que el resultado coincide con los datos esperados
        $this->assertEquals($mockedData, $result);
    }
}
