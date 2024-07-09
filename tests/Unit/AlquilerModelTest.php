<?php

namespace Tests;

use App\Models\AlquilerModel;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as M;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../vendor/autoload.php'; 

class AlquilerModelTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected $mockQuery;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockQuery = M::mock('overload:App\Models\Query'); // Mockear la clase Query utilizada en AlquilerModel
    }

    public function tearDown(): void
    {
        parent::tearDown();
        M::close();
    }

    public function testGetAlquiler()
    {
        // Preparar los datos esperados
        $mockedData = [
            ['id' => 1, 'nombre' => 'Cliente A', 'placa' => 'ABC123', 'modelo' => 'Modelo X', 'documento' => 'Documento A', 'tipo' => 'Tipo A'],
            ['id' => 2, 'nombre' => 'Cliente B', 'placa' => 'XYZ789', 'modelo' => 'Modelo Y', 'documento' => 'Documento B', 'tipo' => 'Tipo B'],
        ];

        // Configurar el comportamiento esperado del mock de Query para el método selectAll
        $this->mockQuery->shouldReceive('selectAll')
            ->once()
            ->andReturn($mockedData);

        // Instanciar el modelo y llamar al método que queremos probar
        $alquilerModel = new AlquilerModel($this->mockQuery);
        $result = $alquilerModel->getAlquiler();

        // Asegurar que el resultado coincide con los datos esperados
        $this->assertEquals($mockedData, $result);
    }

    public function testGetEmpresa()
    {
        // Datos simulados que se esperan retornar del método select
        $mockedData = [
            ['id' => 1, 'nombre' => 'Empresa A', 'direccion' => 'Calle Principal', 'telefono' => '123456789'],
        ];

        // Configurar el mock para el método select en la consulta de empresa
        $this->mockQuery->shouldReceive('select')
            ->with("SELECT * FROM configuracion")
            ->andReturn($mockedData);

        // Instanciar AlquilerModel y llamar al método getEmpresa
        $alquilerModel = new AlquilerModel($this->mockQuery);
        $result = $alquilerModel->getEmpresa();

        // Verificar que el resultado retornado sea el mismo que $mockedData
        $this->assertEquals($mockedData, $result);
    }


    public function testRegistrarAlquilerSuccess()
    {
        // Preparar los datos para el registro de alquiler
        $cantidad = 1;
        $precios = 'tipo';
        $monto = 5;
        $abono = 15;
        $fecha = '2022-05-10';
        $fecha_devolucion = '2022-05-10';
        $observacion = 'si';
        $id_cli = 1;
        $id_veh = 1;
        $documento = 5;
        
        // Configurar el comportamiento esperado del mock de Query para simular que no existe un registro previo
        $this->mockQuery->shouldReceive('select')
            ->once()
            ->andReturnNull();

        // Configurar el comportamiento esperado del mock de Query para el método insertar
        // Simulamos que el insertar devuelve 1 para simular una inserción exitosa
        $this->mockQuery->shouldReceive('insertar')
            ->once()
            ->andReturn(1);

        // Instanciar el modelo y llamar al método que queremos probar
        $alquilerModel = new AlquilerModel($this->mockQuery);
        $result = $alquilerModel->registrarAlquiler($cantidad, $precios, $monto, $abono, $fecha, $fecha_devolucion, $observacion, $id_cli, $id_veh, $documento);

        // Asegurar que el resultado coincide con el valor retornado por insertar (1 en este caso)
        $this->assertEquals(1, $result);
    }

    public function testRegistrarAlquilerError()
    {
        // Preparar los datos para el registro de alquiler
        $cantidad = 1;
        $precios = 'tipo';
        $monto = 5;
        $abono = 15;
        $fecha = '2022-05-10';
        $fecha_devolucion = '2022-05-10';
        $observacion = 'si';
        $id_cli = 1;
        $id_veh = 1;
        $documento = 5;
        
        // Configurar el comportamiento esperado del mock de Query para simular que no existe un registro previo
        $this->mockQuery->shouldReceive('select')
            ->once()
            ->andReturnNull();

        // Configurar el comportamiento esperado del mock de Query para el método insertar
        // Simulamos que el insertar devuelve 0 para simular un fallo en la inserción
        $this->mockQuery->shouldReceive('insertar')
            ->once()
            ->andReturn(0);

        // Instanciar el modelo y llamar al método que queremos probar
        $alquilerModel = new AlquilerModel($this->mockQuery);
        $result = $alquilerModel->registrarAlquiler($cantidad, $precios, $monto, $abono, $fecha, $fecha_devolucion, $observacion, $id_cli, $id_veh, $documento);

        // Asegurar que el resultado coincide con el string "error"
        $this->assertEquals('error', $result);
    }

    // Test para el caso de existencia de alquiler
    public function testRegistrarAlquilerExistente()
    {
        // Datos simulados para el registro de alquiler
        $cantidad = 1;
        $precios = 'Tipo A';
        $monto = 100;
        $abono = 50;
        $fecha = '2024-06-28';
        $fecha_devolucion = '2024-06-30';
        $observacion = 'Alquiler de prueba';
        $id_cli = 1;
        $id_veh = 1;
        $documento = 1;

        // Configurar el mock para el método select en la verificación de existencia de alquiler
        $this->mockQuery->shouldReceive('select')
            ->with("SELECT * FROM alquiler WHERE id_cliente = $id_cli AND id_vehiculo = $id_veh AND id_doc = $documento AND estado = 1")
            ->andReturn([['id' => 1]]); // Alquiler existe

        // Instanciar AlquilerModel y llamar al método que queremos probar
        $alquilerModel = new AlquilerModel($this->mockQuery);
        $result = $alquilerModel->registrarAlquiler($cantidad, $precios, $monto, $abono, $fecha, $fecha_devolucion, $observacion, $id_cli, $id_veh, $documento);

        // Verificar que se retorna el resultado esperado en caso de existencia de alquiler ('existe')
        $this->assertEquals('existe', $result);
    }

    public function testGetDoc()
    {
        // Preparar los datos esperados
        $mockedData = [
            ['id' => 1, 'nombre' => 'Documento A', 'estado' => 1],
            ['id' => 2, 'nombre' => 'Documento B', 'estado' => 1],
        ];

        // Configurar el comportamiento esperado del mock de Query para el método selectAll
        $this->mockQuery->shouldReceive('selectAll')
            ->once()
            ->andReturn($mockedData);

        // Instanciar el modelo y llamar al método que queremos probar
        $alquilerModel = new AlquilerModel($this->mockQuery);
        $result = $alquilerModel->getDoc();

        // Asegurar que el resultado coincide con los datos esperados
        $this->assertEquals($mockedData, $result);
    }

    public function testGetVehiculos()
    {
        // Preparar los datos esperados
        $mockedData = [
            ['id' => 1, 'placa' => 'ABC123', 'id_tipo' => 1, 'id_marca' => 1, 'estado' => 1],
            ['id' => 2, 'placa' => 'XYZ789', 'id_tipo' => 2, 'id_marca' => 2, 'estado' => 1],
        ];

        // Configurar el comportamiento esperado del mock de Query para el método selectAll
        $this->mockQuery->shouldReceive('selectAll')
            ->once()
            ->andReturn($mockedData);

        // Instanciar el modelo y llamar al método que queremos probar
        $alquilerModel = new AlquilerModel($this->mockQuery);
        $result = $alquilerModel->getVehiculos();

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
        $alquilerModel = new AlquilerModel($this->mockQuery);
        $result = $alquilerModel->getVehiculo($vehiculoId);

        // Asegurar que el resultado coincide con los datos esperados
        $this->assertEquals($mockedData, $result);
    }

    public function testVerify()
    {
        // Preparar los datos esperados
        $mockedData = ['id' => 1, 'fecha_prestamo' => '2022-05-10', 'fecha_devolucion' => '2022-05-15']; // Suponiendo un resultado de reserva superpuesta

        // Configurar el comportamiento esperado del mock de Query para el método select
        $this->mockQuery->shouldReceive('select')
            ->once()
            ->andReturn($mockedData);

        // Parámetros para verificar la reserva
        $desde = '2022-05-12';
        $hasta = '2022-05-14';
        $id_veh = 1;

        // Instanciar el modelo y llamar al método que queremos probar
        $alquilerModel = new AlquilerModel($this->mockQuery);
        $result = $alquilerModel->verify($desde, $hasta, $id_veh);

        // Asegurar que el resultado coincide con los datos esperados
        $this->assertEquals($mockedData, $result);
    }

    public function testActualizarVehiculo()
    {
        // Preparar los datos para la actualización del vehículo
        $estado = 0; // Estado a actualizar
        $vehiculoId = 1; // ID del vehículo

        // Configurar el comportamiento esperado del mock de Query para el método save
        $this->mockQuery->shouldReceive('save')
            ->once()
            ->andReturn(1); // Suponiendo que se actualiza correctamente

        // Instanciar el modelo y llamar al método que queremos probar
        $alquilerModel = new AlquilerModel($this->mockQuery);
        $result = $alquilerModel->actualizarVehiculo($estado, $vehiculoId);

        // Asegurar que el resultado coincide con 'ok'
        $this->assertEquals('ok', $result);
    }

    public function testActualizarVehiculoError()
    {
        // Datos simulados para la actualización del vehículo
        $estado = 1;
        $id = 1;

        // Configurar el mock para el método save en la actualización de vehículo
        $this->mockQuery->shouldReceive('save')
            ->once()
            ->andReturn(0); // Simular que hubo un error en la actualización

        // Instanciar AlquilerModel y llamar al método que queremos probar
        $alquilerModel = new AlquilerModel($this->mockQuery);
        $result = $alquilerModel->actualizarVehiculo($estado, $id);

        // Verificar que se retorna el resultado esperado en caso de error en la actualización ('error')
        $this->assertEquals('error', $result);
    }


    public function testProcesarEntrega()
    {
        // Preparar los datos para procesar la entrega
        $estado = 1; // Estado a actualizar
        $alquilerId = 1; // ID del alquiler

        // Configurar el comportamiento esperado del mock de Query para el método save
        $this->mockQuery->shouldReceive('save')
            ->once()
            ->andReturn(1); // Suponiendo que se actualiza correctamente

        // Instanciar el modelo y llamar al método que queremos probar
        $alquilerModel = new AlquilerModel($this->mockQuery);
        $result = $alquilerModel->procesarEntrega($estado, $alquilerId);

        // Asegurar que el resultado coincide con 'ok'
        $this->assertEquals('ok', $result);
    }

    public function testProcesarEntregaError()
    {
        // Datos simulados para procesar la entrega
        $estado = 2; // Nuevo estado
        $id = 1; // ID del registro de alquiler a procesar

        // Configurar el mock para el método save en la actualización del estado de alquiler
        $this->mockQuery->shouldReceive('save')
            ->once()
            ->andReturn(0); // Simular que hubo un error en la actualización

        // Instanciar AlquilerModel y llamar al método que queremos probar
        $alquilerModel = new AlquilerModel($this->mockQuery);
        $result = $alquilerModel->procesarEntrega($estado, $id);

        // Verificar que se retorna el resultado esperado en caso de error en la actualización ('error')
        $this->assertEquals('error', $result);
    }


    public function testVerPrestamo()
    {
        // Preparar los datos esperados
        $mockedData = [
            'id' => 1,
            'fecha_prestamo' => '2022-05-10',
            'fecha_devolucion' => '2022-05-15',
            'dni' => '12345678',
            'nombre' => 'Cliente A',
            'telefono' => '123456789',
            'direccion' => 'Calle A',
            'placa' => 'ABC123',
            'modelo' => 'Modelo X',
            'documento' => 'Documento A',
            'tipo' => 'Tipo A',
        ];

        // Configurar el comportamiento esperado del mock de Query para el método select
        $this->mockQuery->shouldReceive('select')
            ->once()
            ->andReturn($mockedData);

        // ID del préstamo a consultar
        $prestamoId = 1;

        // Instanciar el modelo y llamar al método que queremos probar
        $alquilerModel = new AlquilerModel($this->mockQuery);
        $result = $alquilerModel->verPrestamo($prestamoId);

        // Asegurar que el resultado coincide con los datos esperados
        $this->assertEquals($mockedData, $result);
    }
}