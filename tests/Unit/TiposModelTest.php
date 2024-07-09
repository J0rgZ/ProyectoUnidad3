<?php

namespace Tests;

use App\Models\TiposModel;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as M;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../vendor/autoload.php'; 

class TiposModelTest extends TestCase
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
    
    public function testGetTipos()
    {
        // Estado de los tipos a buscar
        $estado = 1;

        // Datos simulados de tipos que se esperan retornar
        $mockedData = [
            ['id' => 1, 'tipo' => 'Tipo A', 'estado' => 1],
            ['id' => 2, 'tipo' => 'Tipo B', 'estado' => 1],
        ];

        // Configuración de mock para el método selectAll en el modelo
        $sql = "SELECT * FROM tipos WHERE estado = $estado";
        $this->mockQuery->shouldReceive('selectAll')
            ->with($sql)
            ->andReturn($mockedData);

        // Instanciar TiposModel y llamar al método getTipos
        $tiposModel = new TiposModel();
        $result = $tiposModel->getTipos($estado);

        // Verificar que el resultado coincida con los datos esperados
        $this->assertEquals($mockedData, $result);
    }

    public function testRegistrarTipo()
    {
        // Tipo a registrar
        $tipo = 'Nuevo Tipo';

        // Configuración de mock para verificar si el tipo ya existe (caso no existente)
        $this->mockQuery->shouldReceive('select')
            ->andReturn([]); // Simular que no existe ningún tipo con este nombre

        // Configuración de mock para el método save en el registro de tipo (caso exitoso)
        $this->mockQuery->shouldReceive('save')
            ->andReturn(1); // Simular que la inserción fue exitosa

        // Instanciar TiposModel y llamar al método registrarTipo
        $tiposModel = new TiposModel();
        $result = $tiposModel->registrarTipo($tipo);

        // Verificar que el resultado sea "ok" (registro exitoso)
        $this->assertEquals('ok', $result);
    }

    public function testRegistrarTipoExistente()
    {
        // Tipo a registrar
        $tipo = 'Nuevo Tipo';

        // Configuración de mock para verificar si el tipo ya existe (caso existente)
        $this->mockQuery->shouldReceive('select')
            ->andReturn(['id' => 1, 'tipo' => 'Nuevo Tipo']); // Simular que ya existe un tipo con este nombre

        // Instanciar TiposModel y llamar al método registrarTipo
        $tiposModel = new TiposModel();
        $result = $tiposModel->registrarTipo($tipo);

        // Verificar que el resultado sea "existe" (tipo ya existe)
        $this->assertEquals('existe', $result);
    }

    public function testRegistrarTipoError()
    {
        // Tipo a registrar
        $tipo = 'Nuevo Tipo';

        // Configuración de mock para verificar si el tipo ya existe (caso no existente)
        $this->mockQuery->shouldReceive('select')
            ->andReturn([]); // Simular que no existe ningún tipo con este nombre

        // Configuración de mock para el método save en el registro de tipo (caso de error)
        $this->mockQuery->shouldReceive('save')
            ->andReturn(0); // Simular que hubo un error en la inserción

        // Instanciar TiposModel y llamar al método registrarTipo
        $tiposModel = new TiposModel();
        $result = $tiposModel->registrarTipo($tipo);

        // Verificar que el resultado sea "error" (error en la inserción)
        $this->assertEquals('error', $result);
    }




    public function testModificarTipo()
    {
        // ID del tipo a modificar
        $tipoId = 1;
        $nuevoTipo = 'Tipo Modificado';

        // Configuración de mock para el método save en la modificación de tipo
        $this->mockQuery->shouldReceive('save')
            ->andReturn(1); // Simular que la actualización fue exitosa

        // Instanciar TiposModel y llamar al método modificarTipo
        $tiposModel = new TiposModel();
        $result = $tiposModel->modificarTipo($nuevoTipo, $tipoId);

        // Verificar que el resultado sea "modificado" (actualización exitosa)
        $this->assertEquals('modificado', $result);
    }

    public function testModificarTipoError()
    {
        // ID del tipo a modificar
        $tipoId = 1;
        $nuevoTipo = 'Tipo Modificado';

        // Configuración de mock para el método save en la modificación de tipo (caso de error)
        $this->mockQuery->shouldReceive('save')
            ->andReturn(0); // Simular que hubo un error en la actualización

        // Instanciar TiposModel y llamar al método modificarTipo
        $tiposModel = new TiposModel();
        $result = $tiposModel->modificarTipo($nuevoTipo, $tipoId);

        // Verificar que el resultado sea "error" (error en la actualización)
        $this->assertEquals('error', $result);
    }


    public function testEditarTipo()
    {
        // ID del tipo a editar
        $tipoId = 1;

        // Datos simulados del tipo que se espera retornar
        $mockedTipo = [
            'id' => 1,
            'tipo' => 'Tipo A',
            'estado' => 1,
        ];

        // Configuración de mock para el método select en el modelo
        $sql = "SELECT * FROM tipos WHERE id = $tipoId";
        $this->mockQuery->shouldReceive('select')
            ->with($sql)
            ->andReturn($mockedTipo);

        // Instanciar TiposModel y llamar al método editarTipo
        $tiposModel = new TiposModel();
        $result = $tiposModel->editarTipo($tipoId);

        // Verificar que el resultado coincida con los datos esperados
        $this->assertEquals($mockedTipo, $result);
    }

    public function testEditarTipoNoExistente()
    {
        // ID del tipo a editar
        $tipoId = 999; // ID que no existe

        // Configuración de mock para el método select en el modelo (caso no existente)
        $sql = "SELECT * FROM tipos WHERE id = $tipoId";
        $this->mockQuery->shouldReceive('select')
            ->with($sql)
            ->andReturn(null); // Simular que no se encontró ningún tipo con ese ID

        // Instanciar TiposModel y llamar al método editarTipo
        $tiposModel = new TiposModel();
        $result = $tiposModel->editarTipo($tipoId);

        // Verificar que el resultado sea null (tipo no encontrado)
        $this->assertNull($result);
    }


    public function testAccionTipo()
    {
        // ID del tipo a modificar
        $tipoId = 1;
        $nuevoEstado = 0;

        // Configuración de mock para el método save en la acción de tipo
        $this->mockQuery->shouldReceive('save')
            ->andReturn(1); // Simular que la actualización fue exitosa

        // Instanciar TiposModel y llamar al método accionTipo
        $tiposModel = new TiposModel();
        $result = $tiposModel->accionTipo($nuevoEstado, $tipoId);

        // Verificar que el resultado sea 1 (éxito en la acción)
        $this->assertEquals(1, $result);
    }

    public function testAccionTipoError()
    {
        // ID del tipo a modificar
        $tipoId = 1;
        $nuevoEstado = 0;

        // Configuración de mock para el método save en la acción de tipo (caso de error)
        $this->mockQuery->shouldReceive('save')
            ->andReturn(0); // Simular que hubo un error en la actualización

        // Instanciar TiposModel y llamar al método accionTipo
        $tiposModel = new TiposModel();
        $result = $tiposModel->accionTipo($nuevoEstado, $tipoId);

        // Verificar que el resultado sea 0 (error en la acción)
        $this->assertEquals(0, $result);
    }
}

