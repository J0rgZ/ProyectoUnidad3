<?php

namespace Tests;

use App\Models\PricingModel;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as M;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../vendor/autoload.php';

class PricingModelTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected $mockQuery;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockQuery = M::mock('overload:App\Models\Query'); // Mockear la clase Query utilizada en PricingModel
    }

    public function tearDown(): void
    {
        parent::tearDown();
        M::close();
    }

    public function testGetVehiculos()
    {
        // Estado simulado
        $estado = 1;

        // Datos simulados que se esperan retornar del método selectAll
        $mockedData = [
            ['id' => 1, 'nombre' => 'Vehículo A', 'marca' => 'Marca A', 'tipo' => 'Tipo A', 'estado' => 1],
            ['id' => 2, 'nombre' => 'Vehículo B', 'marca' => 'Marca B', 'tipo' => 'Tipo B', 'estado' => 1],
        ];

        // Configurar el mock para el método selectAll en la consulta de vehículos
        $this->mockQuery->shouldReceive('selectAll')
            ->once()
            ->with("SELECT v.*, m.marca, t.tipo FROM vehiculos v INNER JOIN marcas m ON v.id_marca = m.id INNER JOIN tipos t ON v.id_tipo = t.id WHERE v.estado = $estado")
            ->andReturn($mockedData);

        // Instanciar PricingModel y llamar al método getVehiculos
        $pricingModel = new PricingModel();
        $result = $pricingModel->getVehiculos($estado);

        // Verificar que el resultado retornado sea el mismo que $mockedData
        $this->assertEquals($mockedData, $result);
    }
}
