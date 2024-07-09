<?php

namespace Tests\Unit;

use App\Models\DashboardModel;
use Mockery as M;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../vendor/autoload.php';

class DashboardModelTest extends TestCase
{
    protected $mockQuery;

    public function setUp(): void
    {
        parent::setUp();
        
        // Configurar el mock para el método select en el modelo DashboardModel
        $this->mockQuery = M::mock('overload:App\Models\Query');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        M::close();
    }

    public function testGetDatos()
    {
        // Datos simulados que se esperan retornar del método select
        $mockedData = [['total' => 10]];

        // Configurar el mock para el método select en la consulta de getDatos
        $this->mockQuery->shouldReceive('select')
            ->with("SELECT COUNT(*) AS total FROM usuarios WHERE estado = 1")
            ->andReturn($mockedData);

        // Instanciar DashboardModel con el mock de Query
        $dashboardModel = new DashboardModel($this->mockQuery);

        // Llamar al método getDatos y almacenar el resultado
        $result = $dashboardModel->getDatos('usuarios');

        // Verificar que el resultado retornado sea el mismo que $mockedData
        $this->assertEquals($mockedData, $result);
    }

    public function testRentas()
    {
        // Datos simulados que se esperan retornar del método select
        $mockedData = [['ene' => 100, 'feb' => 200, 'mar' => 150]];

        // Configurar el mock para el método select en la consulta de rentas
        $this->mockQuery->shouldReceive('select')
            ->with(M::on(function ($sql) {
                // Verificar que el SQL contenga las fechas esperadas
                return strpos($sql, "BETWEEN '2024-01-01' AND '2024-12-31'") !== false;
            }))
            ->andReturn($mockedData);

        // Instanciar DashboardModel con el mock de Query
        $dashboardModel = new DashboardModel($this->mockQuery);

        // Llamar al método rentas y almacenar el resultado
        $result = $dashboardModel->rentas('2024-01-01', '2024-12-31');

        // Verificar que el resultado retornado sea el mismo que $mockedData
        $this->assertEquals($mockedData, $result);
    }

    public function testRentasSemana()
    {
        // Datos simulados que se esperan retornar del método selectAll
        $mockedData = [
            ['fecha' => '2024-06-24', 'total' => 50],
            ['fecha' => '2024-06-25', 'total' => 100],
            ['fecha' => '2024-06-26', 'total' => 80],
        ];

        // Configurar el mock para el método selectAll en la consulta de rentasSemana
        $this->mockQuery->shouldReceive('selectAll')
            ->with(M::on(function ($sql) {
                // Verificar que el SQL incluya la semana actual
                return strpos($sql, "WEEK(fecha_prestamo) = WEEK(CURDATE())") !== false;
            }))
            ->andReturn($mockedData);

        // Instanciar DashboardModel con el mock de Query
        $dashboardModel = new DashboardModel($this->mockQuery);

        // Llamar al método rentasSemana y almacenar el resultado
        $result = $dashboardModel->rentasSemana();

        // Verificar que el resultado retornado sea el mismo que $mockedData
        $this->assertEquals($mockedData, $result);
    }


}
