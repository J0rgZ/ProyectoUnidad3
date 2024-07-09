<?php

namespace Tests;

use App\Models\VehiculosModel;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as M;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../vendor/autoload.php'; 

class VehiculosModelTest extends TestCase
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

    public function testGetDatos()
    {
        // Configurar el mock para simular la llamada a selectAll con la consulta correcta
        $this->mockQuery->shouldReceive('selectAll')
                        ->with('SELECT * FROM tabla WHERE estado = 1')
                        ->andReturn([
                            ['id' => 1, 'campo1' => 'valor1', 'estado' => 1],
                            ['id' => 2, 'campo1' => 'valor2', 'estado' => 1],
                        ]);

        $vehiculosModel = new VehiculosModel();

        // Llamar al método y almacenar el resultado
        $result = $vehiculosModel->getDatos('tabla');

        // Verificar que el resultado obtenido es el esperado
        $this->assertEquals([
            ['id' => 1, 'campo1' => 'valor1', 'estado' => 1],
            ['id' => 2, 'campo1' => 'valor2', 'estado' => 1],
        ], $result);
    }

    public function testGetVehiculos()
    {
        // Estado de vehículos a buscar
        $estado = 1;

        // Configurar el mock para simular la llamada a selectAll con la consulta correcta
        $this->mockQuery->shouldReceive('selectAll')
                        ->with("SELECT v.*, m.marca, t.tipo FROM vehiculos v INNER JOIN marcas m ON v.id_marca = m.id INNER JOIN tipos t ON v.id_tipo = t.id WHERE v.estado = $estado")
                        ->andReturn([
                            ['id' => 1, 'placa' => 'ABC123', 'marca' => 'Marca A', 'tipo' => 'Tipo A', 'estado' => 1],
                            ['id' => 2, 'placa' => 'XYZ789', 'marca' => 'Marca B', 'tipo' => 'Tipo B', 'estado' => 1],
                        ]);

        $vehiculosModel = new VehiculosModel();

        // Llamar al método y almacenar el resultado
        $result = $vehiculosModel->getVehiculos($estado);

        // Verificar que el resultado obtenido es el esperado
        $this->assertEquals([
            ['id' => 1, 'placa' => 'ABC123', 'marca' => 'Marca A', 'tipo' => 'Tipo A', 'estado' => 1],
            ['id' => 2, 'placa' => 'XYZ789', 'marca' => 'Marca B', 'tipo' => 'Tipo B', 'estado' => 1],
        ], $result);
    }

    public function testVehiculos()
    {
        // Configurar el mock para simular la llamada a selectAll con la consulta correcta
        $this->mockQuery->shouldReceive('selectAll')
                        ->with("SELECT v.*, m.marca, t.tipo FROM vehiculos v INNER JOIN marcas m ON v.id_marca = m.id INNER JOIN tipos t ON v.id_tipo = t.id WHERE v.estado = 1 OR v.estado = 2")
                        ->andReturn([
                            ['id' => 1, 'placa' => 'ABC123', 'marca' => 'Marca A', 'tipo' => 'Tipo A', 'estado' => 1],
                            ['id' => 2, 'placa' => 'XYZ789', 'marca' => 'Marca B', 'tipo' => 'Tipo B', 'estado' => 2],
                        ]);

        $vehiculosModel = new VehiculosModel();

        // Llamar al método y almacenar el resultado
        $result = $vehiculosModel->vehiculos();

        // Verificar que el resultado obtenido es el esperado
        $this->assertEquals([
            ['id' => 1, 'placa' => 'ABC123', 'marca' => 'Marca A', 'tipo' => 'Tipo A', 'estado' => 1],
            ['id' => 2, 'placa' => 'XYZ789', 'marca' => 'Marca B', 'tipo' => 'Tipo B', 'estado' => 2],
        ], $result);
    }

    public function testRegistrarVehiculo()
    {
        // Placa de vehículo que no existe
        $placaNueva = 'XYZ789';

        // Configurar el mock para que select devuelva que el vehículo no existe
        $this->mockQuery->shouldReceive('select')
                        ->with("SELECT * FROM vehiculos WHERE placa = '$placaNueva'")
                        ->andReturn([]); // Simular que el vehículo no existe

        // Configurar el mock para el método save en el registro de vehículo (caso exitoso)
        $this->mockQuery->shouldReceive('save')
                        ->andReturn(1); // Simular que la inserción fue exitosa

        $vehiculosModel = new VehiculosModel();
        
        // Llamar al método y almacenar el resultado
        $result = $vehiculosModel->registrarVehiculo(
            $placaNueva, 10, 100, 1000, 'Modelo B', 60000, 'Manual', 5, 'Mediano', 'Gasolina', 'imagen.jpg', 2, 2
        );

        // Verificar que el resultado retornado sea "ok"
        $this->assertEquals('ok', $result);
    }



    public function testRegistrarVehiculoExistente()
    {
        // Placa de vehículo que ya existe
        $placaExistente = 'ABC123';

        // Configurar el mock para que select devuelva un vehículo existente
        $this->mockQuery->shouldReceive('select')
                        ->with("SELECT * FROM vehiculos WHERE placa = '$placaExistente'")
                        ->andReturn(['id' => 1, 'placa' => $placaExistente]); // Simular que el vehículo ya existe

        $vehiculosModel = new VehiculosModel();
        
        // Llamar al método y almacenar el resultado
        $result = $vehiculosModel->registrarVehiculo(
            $placaExistente, 10, 100, 1000, 'Modelo A', 50000, 'Automatica', 5, 'Grande', 'Gasolina', 'imagen.jpg', 1, 1
        );

        // Verificar que el resultado retornado sea "existe"
        $this->assertEquals('existe', $result);
    }

    public function testRegistrarVehiculoError()
    {
        // Placa de vehículo que no existe
        $placaNueva = 'XYZ789';

        // Configurar el mock para que select devuelva un vehículo que no existe
        $this->mockQuery->shouldReceive('select')
                        ->with("SELECT * FROM vehiculos WHERE placa = '$placaNueva'")
                        ->andReturn([]); // Simular que el vehículo no existe

        // Configurar el mock para que save devuelva 0, indicando error en la inserción
        $this->mockQuery->shouldReceive('save')
                        ->andReturn(0); // Simular que la inserción falla

        $vehiculosModel = new VehiculosModel();
        
        // Llamar al método y almacenar el resultado
        $result = $vehiculosModel->registrarVehiculo(
            $placaNueva, 10, 100, 1000, 'Modelo B', 60000, 'Manual', 4, 'Mediano', 'Gasolina', 'imagen.jpg', 2, 2
        );

        // Verificar que el resultado retornado sea "error"
        $this->assertEquals('error', $result);
    }

    public function testModificarVehiculo()
    {
        $placa = 'ABC123';
        $precio_hora = 15;
        $precio_dia = 150;
        $precio_mes = 1500;
        $modelo = 'Modelo Actualizado';
        $kilometraje = 60000;
        $transmision = 'Manual';
        $asientos = 4;
        $equipaje = 'Mediano';
        $combustible = 'Gasolina';
        $imgNombre = 'imagen_actualizada.jpg';
        $tipo = 2;
        $marca = 2;
        $id = 1;

        // Configurar mock para el método save en la modificación de vehículo
        $this->mockQuery->shouldReceive('save')
                        ->andReturn(1); // Simular que la actualización fue exitosa

        $vehiculosModel = new VehiculosModel();
        
        // Llamar al método y almacenar el resultado
        $result = $vehiculosModel->modificarVehiculo(
            $placa, $precio_hora, $precio_dia, $precio_mes, $modelo, $kilometraje, $transmision,
            $asientos, $equipaje, $combustible, $imgNombre, $tipo, $marca, $id
        );

        // Verificar que el resultado retornado sea "modificado"
        $this->assertEquals('modificado', $result);
    }

    public function testModificarVehiculoError()
    {
        // Datos del vehículo a modificar
        $placa = 'ABC123';
        $precio_hora = 10;
        $precio_dia = 100;
        $precio_mes = 1000;
        $modelo = 'Modelo A';
        $kilometraje = 50000;
        $transmision = 'Automatica';
        $asientos = 5;
        $equipaje = 'Grande';
        $combustible = 'Gasolina';
        $imgNombre = 'imagen.jpg';
        $tipo = 1;
        $marca = 1;
        $id = 1;

        // Configurar el mock para simular la llamada a save con el SQL y los datos
        $this->mockQuery->shouldReceive('save')
                        ->andReturn(0); // Simular que la modificación falla y no se realiza

        $vehiculosModel = new VehiculosModel();

        // Llamar al método y almacenar el resultado
        $result = $vehiculosModel->modificarVehiculo(
            $placa, $precio_hora, $precio_dia, $precio_mes,
            $modelo, $kilometraje, $transmision, $asientos, $equipaje,
            $combustible, $imgNombre, $tipo, $marca, $id
        );

        // Verificar que el resultado retornado es "error"
        $this->assertEquals('error', $result);
    }


    public function testEditarVehiculo()
    {
        $idVehiculo = 1;

        // Datos simulados del vehículo que se espera retornar
        $mockedVehiculo = [
            'id' => $idVehiculo,
            'placa' => 'ABC123',
            'precio_hora' => 10,
            'precio_dia' => 100,
            'precio_mes' => 1000,
            'modelo' => 'Modelo A',
            'kilometraje' => 50000,
            'transmision' => 'Automatica',
            'asientos' => 5,
            'equipaje' => 'Grande',
            'combustible' => 'Gasolina',
            'foto' => 'imagen.jpg',
            'id_tipo' => 1,
            'id_marca' => 1,
            'estado' => 1,
        ];

        // Configuración de mock para el método select en el modelo
        $sql = "SELECT * FROM vehiculos WHERE id = $idVehiculo";
        $this->mockQuery->shouldReceive('select')
                        ->with($sql)
                        ->andReturn($mockedVehiculo);

        $vehiculosModel = new VehiculosModel();
        
        // Llamar al método y almacenar el resultado
        $result = $vehiculosModel->editarVeh($idVehiculo);

        // Verificar que el resultado coincida con los datos esperados
        $this->assertEquals($mockedVehiculo, $result);
    }

    public function testAccionVehiculo()
    {
        $estado = 0;
        $idVehiculo = 1;

        // Configurar mock para el método save en la acción de vehículo
        $this->mockQuery->shouldReceive('save')
                        ->andReturn(1); // Simular que la actualización fue exitosa

        $vehiculosModel = new VehiculosModel();
        
        // Llamar al método y almacenar el resultado
        $result = $vehiculosModel->accionVeh($estado, $idVehiculo);

        // Verificar que el resultado sea 1 (éxito en la acción)
        $this->assertEquals(1, $result);
    }

    public function testBuscarVehiculo()
    {
        $valor = 'ABC';

        // Datos simulados de vehículos que se esperan retornar
        $mockedVehiculos = [
            ['id' => 1, 'placa' => 'ABC123', 'tipo' => 'SUV', 'marca' => 'Toyota', 'estado' => 1],
            ['id' => 2, 'placa' => 'XYZ789', 'tipo' => 'Sedan', 'marca' => 'Honda', 'estado' => 1],
        ];

        // Configuración del mock para el método selectAll en el modelo
        $this->mockQuery->shouldReceive('selectAll')
                        ->once() // Asegura que se llame exactamente una vez
                        ->andReturnUsing(function ($sql) use ($valor, $mockedVehiculos) {
                            // Verificar que la consulta contenga el valor buscado
                            if (strpos($sql, $valor) !== false) {
                                return $mockedVehiculos;
                            }
                            return [];
                        });

        // Instanciamos el modelo de vehículos
        $vehiculosModel = new VehiculosModel();
        
        // Llamamos al método buscarVehiculo y almacenamos el resultado
        $result = $vehiculosModel->buscarVehiculo($valor);

        // Verificamos que el resultado coincida con los datos esperados
        $this->assertEquals($mockedVehiculos, $result);
    }
}

