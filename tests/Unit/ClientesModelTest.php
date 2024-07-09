<?php

namespace Tests;

use App\Models\ClientesModel;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as M;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../vendor/autoload.php';

class ClientesModelTest extends TestCase
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

    public function testGetClientes()
    {
        $mockedData = [
            ['id' => 1, 'nombre' => 'Cliente 1', 'direccion' => 'Dirección 1'],
            ['id' => 2, 'nombre' => 'Cliente 2', 'direccion' => 'Dirección 2'],
        ];

        // Configurar el mock para selectAll en getClientes
        $this->mockQuery->shouldReceive('selectAll')
            ->once()
            ->with("SELECT * FROM clientes WHERE estado = 1")
            ->andReturn($mockedData);

        $clientesModel = new ClientesModel($this->mockQuery);
        $result = $clientesModel->getClientes(1);

        $this->assertEquals($mockedData, $result);
    }

    public function testBuscarCliente()
    {
        $mockedData = [
            ['id' => 1, 'nombre' => 'Cliente 1', 'direccion' => 'Dirección 1'],
            ['id' => 2, 'nombre' => 'Cliente 2', 'direccion' => 'Dirección 2'],
        ];

        // Configurar el mock para selectAll en buscarCliente
        $this->mockQuery->shouldReceive('selectAll')
            ->once()
            ->with("SELECT id, nombre, direccion FROM clientes WHERE nombre LIKE '%valor_buscado%' AND estado = 1")
            ->andReturn($mockedData);

        $clientesModel = new ClientesModel($this->mockQuery);
        $result = $clientesModel->buscarCliente('valor_buscado');

        $this->assertEquals($mockedData, $result);
    }


    public function testRegistrarCliente_Exito()
    {
        // Configurar el mock para el método select en la verificación de existencia de cliente
        $this->mockQuery->shouldReceive('select')
            ->andReturn([]); // Cliente no existe

        // Configurar el mock para el método save en el registro de cliente
        $this->mockQuery->shouldReceive('save')
            ->once()
            ->andReturn(1); // Éxito en la inserción

        $nombre = 'Nuevo Cliente';
        $dni = '12345678A';
        $telefono = '987654321';
        $direccion = 'Calle Principal';

        $clientesModel = new ClientesModel($this->mockQuery);
        $result = $clientesModel->registrarCliente($dni, $nombre, $telefono, $direccion);

        $this->assertEquals('ok', $result); // Verificar que se retorna 'ok' en caso de éxito
    }

    public function testRegistrarCliente_Error()
    {
        // Configurar el mock para el método select en la verificación de existencia de cliente
        $this->mockQuery->shouldReceive('select')
            ->andReturn([]); // Simulamos que el cliente no existe

        // Configurar el mock para el método save en el registro de cliente
        $this->mockQuery->shouldReceive('save')
            ->once()
            ->andReturn(0); // Simulamos que hubo un error en la inserción

        $nombre = 'Nuevo Cliente';
        $dni = '12345678A';
        $telefono = '987654321';
        $direccion = 'Calle Principal';

        $clientesModel = new ClientesModel($this->mockQuery);
        $result = $clientesModel->registrarCliente($dni, $nombre, $telefono, $direccion);

        $this->assertEquals('error', $result); // Verificar que se retorna 'error' cuando falla la inserción
    }

    public function testRegistrarClienteExistente()
    {
        $nombre = 'Cliente Existente';
        $dni = '12345678A';
        $telefono = '987654321';
        $direccion = 'Calle Principal';

        // Configurar el mock para el método select en la verificación de existencia de cliente
        // Simulamos que el cliente ya existe en la base de datos
        $this->mockQuery->shouldReceive('select')
            ->with("SELECT * FROM clientes WHERE nombre = '$nombre'")
            ->andReturn(['id' => 1, 'nombre' => $nombre]);

        $clientesModel = new ClientesModel($this->mockQuery);
        $result = $clientesModel->registrarCliente($dni, $nombre, $telefono, $direccion);

        // Verificar que se retorna 'existe' si el cliente ya existe en la base de datos
        $this->assertEquals('existe', $result);
    }


    public function testModificarCliente_success()
    {
        $mockedDni = '12345678A';
        $mockedNombre = 'Nuevo Nombre';
        $mockedTelefono = '987654321';
        $mockedDireccion = 'Calle Principal';
        $mockedId = 1;

        // Configuración del mock para caso de éxito
        $this->mockQuery->shouldReceive('save')
            ->once()
            ->with("UPDATE clientes SET dni = ?, nombre = ?, telefono = ? ,direccion = ? WHERE id = ?", 
                [$mockedDni, $mockedNombre, $mockedTelefono, $mockedDireccion, $mockedId])
            ->andReturn(1);

        $clientesModel = new ClientesModel($this->mockQuery);
        $result = $clientesModel->modificarCliente($mockedDni, $mockedNombre, $mockedTelefono, $mockedDireccion, $mockedId);

        // Verificar que el método devuelve 'modificado' en caso de éxito
        $this->assertEquals('modificado', $result);
    }

    public function testModificarCliente_failure()
    {
        $mockedDni = '12345678A';
        $mockedNombre = 'Nuevo Nombre';
        $mockedTelefono = '987654321';
        $mockedDireccion = 'Calle Principal';
        $mockedId = 1;

        // Configuración del mock para caso de error
        $this->mockQuery->shouldReceive('save')
            ->once()
            ->with("UPDATE clientes SET dni = ?, nombre = ?, telefono = ? ,direccion = ? WHERE id = ?", 
                [$mockedDni, $mockedNombre, $mockedTelefono, $mockedDireccion, $mockedId])
            ->andReturn(0);

        $clientesModel = new ClientesModel($this->mockQuery);
        $result = $clientesModel->modificarCliente($mockedDni, $mockedNombre, $mockedTelefono, $mockedDireccion, $mockedId);

        // Verificar que el método devuelve 'error' en caso de error
        $this->assertEquals('error', $result);
    }

    public function testEditarCli()
    {
        $mockedId = 1;
        $mockedData = ['id' => 1, 'nombre' => 'Cliente Ejemplo', 'direccion' => 'Calle Principal'];

        // Configurar el mock para select en editarCli
        $this->mockQuery->shouldReceive('select')
            ->once()
            ->with("SELECT * FROM clientes WHERE id = $mockedId")
            ->andReturn($mockedData);

        $clientesModel = new ClientesModel($this->mockQuery);
        $result = $clientesModel->editarCli($mockedId);

        $this->assertEquals($mockedData, $result);
    }

    public function testAccionCli()
    {
        $mockedEstado = 0;
        $mockedId = 1;

        // Configurar el mock para save en accionCli
        $this->mockQuery->shouldReceive('save')
            ->once()
            ->with("UPDATE clientes SET estado = ? WHERE id = ?", [$mockedEstado, $mockedId])
            ->andReturn(1); // Suponiendo que la actualización tiene éxito

        $clientesModel = new ClientesModel($this->mockQuery);
        $result = $clientesModel->accionCli($mockedEstado, $mockedId);

        $this->assertEquals(1, $result); // Verificar que el resultado sea 1 (éxito)
    }

    public function testModificarPass()
    {
        $mockedClave = 'nuevaclave123';
        $mockedId = 1;

        // Configurar el mock para save en modificarPass
        $this->mockQuery->shouldReceive('save')
            ->once()
            ->with("UPDATE clientes SET clave = ? WHERE id = ?", [$mockedClave, $mockedId])
            ->andReturn(1); // Suponiendo que la actualización tiene éxito

        $clientesModel = new ClientesModel($this->mockQuery);
        $result = $clientesModel->modificarPass($mockedClave, $mockedId);

        $this->assertEquals(1, $result); // Verificar que el resultado sea 1 (éxito)
    }

    public function testModificarDato_ActualizacionExitosa()
    {
        $nombre = 'Nuevo Nombre';
        $dni = '12345678A';
        $correo = 'nuevo@correo.com';
        $tel = '987654321';
        $dir = 'Calle Principal';
        $id = 1;

        // Configurar el mock para el método save en la modificación de datos del cliente
        $this->mockQuery->shouldReceive('save')
            ->with("UPDATE clientes SET nombre=?, dni=?, correo=?, telefono=?, direccion=? WHERE id=?",
                [$nombre, $dni, $correo, $tel, $dir, $id])
            ->andReturn(1); // Simulamos que la actualización tiene éxito

        $clientesModel = new ClientesModel($this->mockQuery);
        $result = $clientesModel->modificarDato($nombre, $dni, $correo, $tel, $dir, $id);

        // Verificar que se retorna 1 cuando la actualización tiene éxito
        $this->assertEquals(1, $result);
    }

    public function testModificarDato_ActualizacionNoExitosa()
    {
        $nombre = 'Nuevo Nombre';
        $dni = '12345678A';
        $correo = 'nuevo@correo.com';
        $tel = '987654321';
        $dir = 'Calle Principal';
        $id = 1;

        // Configurar el mock para el método save en la modificación de datos del cliente
        $this->mockQuery->shouldReceive('save')
            ->with("UPDATE clientes SET nombre=?, dni=?, correo=?, telefono=?, direccion=? WHERE id=?",
                [$nombre, $dni, $correo, $tel, $dir, $id])
            ->andReturn(0); // Simulamos que la actualización no tiene éxito

        $clientesModel = new ClientesModel($this->mockQuery);
        $result = $clientesModel->modificarDato($nombre, $dni, $correo, $tel, $dir, $id);

        // Verificar que se retorna 0 cuando la actualización no tiene éxito
        $this->assertEquals(0, $result);
    }


}
