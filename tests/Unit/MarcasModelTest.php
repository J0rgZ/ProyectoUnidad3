<?php

namespace Tests\Unit;

use App\Models\MarcasModel;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as M;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../vendor/autoload.php';

class MarcasModelTest extends TestCase
{
    protected $mockQuery;

    public function setUp(): void
    {
        parent::setUp();
        
        // Configurar el mock para el método select, selectAll y save en el modelo MarcasModel
        $this->mockQuery = M::mock('overload:App\Models\Query');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        M::close(); // Asegura que se cierren los mocks después de cada prueba
    }

    public function testGetMarcas()
    {
        // Datos simulados que se esperan retornar del método selectAll
        $estado = 1;
        $mockedData = [
            ['id' => 1, 'marca' => 'Marca A', 'estado' => 1],
            ['id' => 2, 'marca' => 'Marca B', 'estado' => 1],
        ];

        // Configurar el mock para el método selectAll en la consulta de getMarcas
        $this->mockQuery->shouldReceive('selectAll')
            ->with("SELECT * FROM marcas WHERE estado = $estado")
            ->andReturn($mockedData);

        // Instanciar MarcasModel con el mock de Query
        $marcasModel = new MarcasModel();

        // Llamar al método getMarcas y almacenar el resultado
        $result = $marcasModel->getMarcas($estado);

        // Verificar que el resultado retornado sea el mismo que $mockedData
        $this->assertEquals($mockedData, $result);
    }

    public function testRegistrarMarca()
    {
        // Datos simulados para la inserción
        $marcaNueva = 'Nueva Marca';

        // Configurar el mock para el método select y save en la consulta de registrarMarca
        $this->mockQuery->shouldReceive('select')
            ->with("SELECT * FROM marcas WHERE marca = '$marcaNueva'")
            ->andReturn([]); // Simular que la marca no existe

        $this->mockQuery->shouldReceive('save')
            ->with("INSERT INTO marcas(marca) VALUES (?)", [$marcaNueva])
            ->andReturn(1); // Supongamos que devuelve 1 (éxito)

        // Instanciar MarcasModel con el mock de Query
        $marcasModel = new MarcasModel();

        // Llamar al método registrarMarca y almacenar el resultado
        $result = $marcasModel->registrarMarca($marcaNueva);

        // Verificar que el resultado retornado sea "ok"
        $this->assertEquals('ok', $result);
    }
    

    public function testRegistrarMarcaExistente()
    {
        // Marca que ya existe en la base de datos
        $marcaExistente = 'MarcaExistente';

        // Configurar el mock para el método select en la consulta de registrarMarca
        $this->mockQuery->shouldReceive('select')
            ->with("SELECT * FROM marcas WHERE marca = '$marcaExistente'")
            ->andReturn([['id' => 1, 'marca' => $marcaExistente]]); // Simular que la marca existe

        // No se debe llamar a save, así que no configuramos expectativa para save

        // Instanciar MarcasModel con el mock de Query
        $marcasModel = new MarcasModel();

        // Llamar al método registrarMarca con la marca existente y almacenar el resultado
        $result = $marcasModel->registrarMarca($marcaExistente);

        // Verificar que el resultado retornado sea "existe"
        $this->assertEquals('existe', $result);
    }

    public function testRegistrarMarcaError()
    {
        // Marca a registrar
        $marcaNueva = 'Nueva Marca';

        // Configurar el mock para el método select y save en la consulta de registrarMarca
        $this->mockQuery->shouldReceive('select')
            ->with("SELECT * FROM marcas WHERE marca = '$marcaNueva'")
            ->andReturn([]); // Simular que la marca no existe

        // Configurar el mock para simular un error en la inserción
        $this->mockQuery->shouldReceive('save')
            ->with("INSERT INTO marcas(marca) VALUES (?)", [$marcaNueva])
            ->andReturn(0); // Simular que devuelve 0 (error)

        // Instanciar MarcasModel con el mock de Query
        $marcasModel = new MarcasModel();

        // Llamar al método registrarMarca y almacenar el resultado
        $result = $marcasModel->registrarMarca($marcaNueva);

        // Verificar que el resultado retornado sea "error"
        $this->assertEquals('error', $result);
    }



    public function testModificarMarca()
    {
        // Datos simulados para la modificación
        $marcaModificada = 'Marca Modificada';
        $idMarca = 1;

        // Configurar el mock para el método save en la consulta de modificarMarca
        $this->mockQuery->shouldReceive('save')
            ->with("UPDATE marcas SET marca = ? WHERE id = ?", [$marcaModificada, $idMarca])
            ->andReturn(1); // Supongamos que devuelve 1 (éxito)

        // Instanciar MarcasModel con el mock de Query
        $marcasModel = new MarcasModel();

        // Llamar al método modificarMarca y almacenar el resultado
        $result = $marcasModel->modificarMarca($marcaModificada, $idMarca);

        // Verificar que el resultado retornado sea "modificado"
        $this->assertEquals('modificado', $result);
    }

    public function testModificarMarcaError()
    {
        // Datos simulados para la modificación
        $marcaModificada = 'Marca Modificada';
        $idMarca = 1;

        // Configurar el mock para el método save en la consulta de modificarMarca
        $this->mockQuery->shouldReceive('save')
            ->with("UPDATE marcas SET marca = ? WHERE id = ?", [$marcaModificada, $idMarca])
            ->andReturn(0); // Supongamos que devuelve 0 (error)

        // Instanciar MarcasModel con el mock de Query
        $marcasModel = new MarcasModel();

        // Llamar al método modificarMarca y almacenar el resultado
        $result = $marcasModel->modificarMarca($marcaModificada, $idMarca);

        // Verificar que el resultado retornado sea "error"
        $this->assertEquals('error', $result);
    }


    public function testEditarMarca()
    {
        // Datos simulados para la edición
        $idMarca = 1;
        $mockedData = ['id' => 1, 'marca' => 'Marca A', 'estado' => 1];

        // Configurar el mock para el método select en la consulta de editarMarca
        $this->mockQuery->shouldReceive('select')
            ->with("SELECT * FROM marcas WHERE id = $idMarca")
            ->andReturn($mockedData);

        // Instanciar MarcasModel con el mock de Query
        $marcasModel = new MarcasModel();

        // Llamar al método editarMarca y almacenar el resultado
        $result = $marcasModel->editarMarca($idMarca);

        // Verificar que el resultado retornado sea el mismo que $mockedData
        $this->assertEquals($mockedData, $result);
    }

    public function testAccionMarca()
    {
        // Datos simulados para la acción de cambio de estado
        $estadoNuevo = 0;
        $idMarca = 1;

        $this->mockQuery->shouldReceive('save')
        ->with("UPDATE marcas SET estado = ? WHERE id = ?", [$estadoNuevo, $idMarca])
        ->andReturn(true); // Devolver true en lugar de 1

        // Instanciar MarcasModel con el mock de Query
        $marcasModel = new MarcasModel();

        // Llamar al método accionMarca y almacenar el resultado
        $result = $marcasModel->accionMarca($estadoNuevo, $idMarca);

        // Verificar que el resultado retornado sea verdadero (éxito)
        $this->assertTrue($result);
    }
}

?>
