<?php

namespace Tests;

use App\Models\ReservasModel;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as M;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../vendor/autoload.php';

class ReservasModelTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected $mockQuery;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockQuery = M::mock('overload:App\Models\Query');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        M::close();
    }

    public function testGetReservasIdVehiculoVacio()
    {
        // SQL esperado cuando id_vehiculo está vacío
        $sql = "SELECT r.*, c.nombre FROM reservas r INNER JOIN clientes c ON r.id_cliente = c.id";

        // Datos simulados que se esperan retornar del método selectAll
        $mockedData = [
            ['id' => 1, 'nombre' => 'Reserva A'],
            ['id' => 2, 'nombre' => 'Reserva B']
        ];

        // Configurar el mock para el método selectAll en la consulta de reservas sin id_vehiculo
        $this->mockQuery->shouldReceive('selectAll')
            ->with($sql)
            ->once() // Esperamos una sola llamada a selectAll con este SQL
            ->andReturn($mockedData);

        // Instanciar ReservasModel y llamar al método getReservas con id_vehiculo vacío
        $reservasModel = new ReservasModel();
        $result = $reservasModel->getReservas('');

        // Verificar que el resultado retornado coincida con $mockedData
        $this->assertEquals($mockedData, $result);
    }

    public function testGetReservasIdVehiculoEspecifico()
    {
        // Valor específico de id_vehiculo
        $id_vehiculo = 1;

        // SQL esperado cuando id_vehiculo tiene un valor específico
        $sql = "SELECT r.*, c.nombre FROM reservas r INNER JOIN clientes c ON r.id_cliente = c.id WHERE r.id_vehiculo = $id_vehiculo";

        // Datos simulados que se esperan retornar del método selectAll
        $mockedData = [
            ['id' => 1, 'nombre' => 'Reserva A']
        ];

        // Configurar el mock para el método selectAll en la consulta de reservas con id_vehiculo específico
        $this->mockQuery->shouldReceive('selectAll')
            ->with($sql)
            ->once() // Esperamos una sola llamada a selectAll con este SQL
            ->andReturn($mockedData);

        // Instanciar ReservasModel y llamar al método getReservas con id_vehiculo específico
        $reservasModel = new ReservasModel();
        $result = $reservasModel->getReservas($id_vehiculo);

        // Verificar que el resultado retornado coincida con $mockedData
        $this->assertEquals($mockedData, $result);
    }

    public function testRegistrarReserva()
    {
        // Datos simulados para la inserción de reserva
        $fecha = '2024-06-10';
        $fecha_devolucion = '2024-06-15';
        $cantidad = 1;
        $tipo = 'tipo';
        $monto = 100.50;
        $fecha_reserva = '2024-06-01';
        $observacion = 'Observación';
        $id_veh = 1;
        $id_cli = 1;

        // Configurar el mock para el método insertar en la inserción de reserva
        $this->mockQuery->shouldReceive('insertar')
            ->once()
            ->andReturn(1); // Simulamos que se insertó correctamente y devolvió un ID

        // Instanciar ReservasModel y llamar al método registrarReserva con datos simulados
        $reservasModel = new ReservasModel();
        $result = $reservasModel->registrarReserva($fecha, $fecha_devolucion, $cantidad, $tipo, $monto, $fecha_reserva, $observacion, $id_veh, $id_cli);

        // Verificar que el ID retornado sea 1
        $this->assertEquals(1, $result);
    }

    public function testRegistrarReserva_Fallo()
    {
        // Datos simulados para la inserción de reserva que falla
        $fecha = '2024-06-10';
        $fecha_devolucion = '2024-06-15';
        $cantidad = 1;
        $tipo = 'tipo';
        $monto = 100.50;
        $fecha_reserva = '2024-06-01';
        $observacion = 'Observación';
        $id_veh = 1;
        $id_cli = 1;

        // Configurar el mock para el método insertar en la inserción de reserva
        $this->mockQuery->shouldReceive('insertar')
            ->once()
            ->andReturn(0); // Simulamos que la inserción falla y devuelve 0

        // Instanciar ReservasModel y llamar al método registrarReserva con datos simulados
        $reservasModel = new ReservasModel();
        $result = $reservasModel->registrarReserva($fecha, $fecha_devolucion, $cantidad, $tipo, $monto, $fecha_reserva, $observacion, $id_veh, $id_cli);

        // Verificar que el resultado sea 0, indicando que la inserción falló
        $this->assertEquals(0, $result);
    }

    public function testGetVehiculos()
    {
        // SQL esperado en el método getVehiculos
        $sql = "SELECT v.*, m.marca, t.tipo FROM vehiculos v INNER JOIN marcas m ON v.id_marca = m.id INNER JOIN tipos t ON v.id_tipo = t.id WHERE v.estado != 0";

        // Datos simulados que se esperan retornar del método selectAll
        $mockedData = [
            ['id' => 1, 'marca' => 'Marca A', 'tipo' => 'Tipo A', 'modelo' => 'Modelo A', 'estado' => 1],
            ['id' => 2, 'marca' => 'Marca B', 'tipo' => 'Tipo B', 'modelo' => 'Modelo B', 'estado' => 1],
        ];

        // Configurar el mock para el método selectAll en la consulta de vehículos
        $this->mockQuery->shouldReceive('selectAll')
            ->with($sql)
            ->once() // Esperamos una sola llamada a selectAll con este SQL
            ->andReturn($mockedData);

        // Instanciar ReservasModel y llamar al método getVehiculos
        $reservasModel = new ReservasModel();
        $result = $reservasModel->getVehiculos();

        // Verificar que el resultado retornado coincida con $mockedData
        $this->assertEquals($mockedData, $result);
    }

    public function testGetVehiculo()
    {
        $id_vehiculo = 1;

        // SQL esperado en el método getVehiculo con el ID específico
        $sql = "SELECT v.*, m.marca, t.tipo FROM vehiculos v INNER JOIN marcas m ON v.id_marca = m.id INNER JOIN tipos t ON v.id_tipo = t.id WHERE v.id = $id_vehiculo";

        // Datos simulados que se esperan retornar del método select
        $mockedData = [
            'id' => 1,
            'marca' => 'Marca A',
            'tipo' => 'Tipo A',
            'modelo' => 'Modelo A',
            'estado' => 1
        ];

        // Configurar el mock para el método select en la consulta de vehículo por ID
        $this->mockQuery->shouldReceive('select')
            ->with($sql)
            ->once() // Esperamos una sola llamada a select con este SQL
            ->andReturn($mockedData);

        // Instanciar ReservasModel y llamar al método getVehiculo con el ID específico
        $reservasModel = new ReservasModel();
        $result = $reservasModel->getVehiculo($id_vehiculo);

        // Verificar que el resultado retornado coincida con $mockedData
        $this->assertEquals($mockedData, $result);
    }

    public function testGetNuevasReservas()
    {
        // SQL esperado en el método getNuevasReservas
        $expectedSql = "SELECT r.id, r.f_reserva, c.nombre FROM reservas r INNER JOIN clientes c ON r.id_cliente = c.id WHERE r.estado = 0 ORDER BY r.id DESC LIMIT 5";

        // Datos simulados que se esperan retornar del método selectAll
        $mockedData = [
            ['id' => 1, 'f_reserva' => '2024-07-01', 'nombre' => 'Cliente A'],
            ['id' => 2, 'f_reserva' => '2024-06-30', 'nombre' => 'Cliente B']
        ];

        // Configurar el mock para el método selectAll en la consulta de nuevas reservas
        $this->mockQuery->shouldReceive('selectAll')
            ->with($expectedSql)
            ->once() // Esperamos una sola llamada a selectAll con este SQL
            ->andReturn($mockedData);

        // Instanciar ReservasModel y llamar al método getNuevasReservas
        $reservasModel = new ReservasModel();
        $result = $reservasModel->getNuevasReservas();

        // Verificar que el resultado retornado coincida con $mockedData
        $this->assertEquals($mockedData, $result);
    }

    public function testGetReserva()
    {
        $id_reserva = 1;

        // SQL esperado en el método getReserva con el ID específico
        $expectedSql = "SELECT r.*, c.nombre, c.correo FROM reservas r INNER JOIN clientes c ON r.id_cliente = c.id WHERE r.id = $id_reserva";

        // Datos simulados que se esperan retornar del método select
        $mockedData = [
            'id' => 1,
            'f_reserva' => '2024-07-01',
            'nombre' => 'Cliente A',
            'correo' => 'clienteA@example.com'
        ];

        // Configurar el mock para el método select en la consulta de reserva por ID
        $this->mockQuery->shouldReceive('select')
            ->with($expectedSql)
            ->once() // Esperamos una sola llamada a select con este SQL
            ->andReturn($mockedData);

        // Instanciar ReservasModel y llamar al método getReserva con el ID específico
        $reservasModel = new ReservasModel();
        $result = $reservasModel->getReserva($id_reserva);

        // Verificar que el resultado retornado coincida con $mockedData
        $this->assertEquals($mockedData, $result);
    }

    public function testActualizarEstado()
    {
        $id_reserva = 1;
        $nuevo_estado = 1; // Nuevo estado que se va a actualizar

        // SQL esperado en el método actualizarEstado
        $expectedSql = "UPDATE reservas SET estado = ? WHERE id = ?";

        // Configurar el mock para el método save en la actualización de estado
        $this->mockQuery->shouldReceive('save')
            ->with($expectedSql, [$nuevo_estado, $id_reserva])
            ->once() // Esperamos una sola llamada a save con este SQL y estos datos
            ->andReturn(1); // Simulamos que la actualización fue exitosa

        // Instanciar ReservasModel y llamar al método actualizarEstado con los parámetros simulados
        $reservasModel = new ReservasModel();
        $result = $reservasModel->actualizarEstado($nuevo_estado, $id_reserva);

        // Verificar que el resultado retornado sea 1, indicando que la actualización fue exitosa
        $this->assertEquals(1, $result);
    }

    public function testGetEmpresa()
    {
        // SQL esperado en el método getEmpresa
        $expectedSql = "SELECT * FROM configuracion";

        // Datos simulados que se esperan retornar del método select
        $mockedData = [
            'nombre' => 'Mi Empresa',
            'direccion' => 'Calle Principal',
            'telefono' => '123456789',
            'email' => 'info@miempresa.com'
        ];

        // Configurar el mock para el método select en la consulta de configuración de empresa
        $this->mockQuery->shouldReceive('select')
            ->with($expectedSql)
            ->once() // Esperamos una sola llamada a select con este SQL
            ->andReturn($mockedData);

        // Instanciar ReservasModel y llamar al método getEmpresa
        $reservasModel = new ReservasModel();
        $result = $reservasModel->getEmpresa();

        // Verificar que el resultado retornado coincida con $mockedData
        $this->assertEquals($mockedData, $result);
    }
}