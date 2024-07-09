<?php

namespace Tests\Unit;

use App\Models\DocumentosModel;
use Mockery as M;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../vendor/autoload.php';

class DocumentosModelTest extends TestCase
{
    protected $mockQuery;

    public function setUp(): void
    {
        parent::setUp();
        
        // Configurar el mock para el método selectAll en el modelo DocumentosModel
        $this->mockQuery = M::mock('overload:App\Models\Query');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        M::close();
    }

    public function testGetDocumentos()
    {
        // Datos simulados que se esperan retornar del método selectAll
        $mockedData = [
            ['id' => 1, 'documento' => 'Documento A', 'estado' => 1],
            ['id' => 2, 'documento' => 'Documento B', 'estado' => 1],
        ];

        // Configurar el mock para el método selectAll en la consulta de getDocumentos
        $this->mockQuery->shouldReceive('selectAll')
            ->with("SELECT * FROM documentos WHERE estado = 1")
            ->andReturn($mockedData);

        // Instanciar DocumentosModel con el mock de Query
        $documentosModel = new DocumentosModel($this->mockQuery);

        // Llamar al método getDocumentos y almacenar el resultado
        $result = $documentosModel->getDocumentos(1);

        // Verificar que el resultado retornado sea el mismo que $mockedData
        $this->assertEquals($mockedData, $result);
    }


    public function testRegistrarDoc()
    {
        // Documento a registrar
        $documento = 'Nuevo Documento';

        // Configurar el mock para el método select en la consulta de verificar existencia
        $this->mockQuery->shouldReceive('select')
            ->with("SELECT * FROM documentos WHERE documento = '$documento'")
            ->andReturn([]); // Simular que el documento no existe

        // Configurar el mock para el método save en la consulta de inserción
        $this->mockQuery->shouldReceive('save')
            ->once()
            ->andReturn(1); // Éxito en la inserción

        // Instanciar DocumentosModel con el mock de Query
        $documentosModel = new DocumentosModel($this->mockQuery);

        // Llamar al método registrarDoc y almacenar el resultado
        $result = $documentosModel->registrarDoc($documento);

        // Verificar que el resultado sea "ok" (éxito en la inserción)
        $this->assertEquals('ok', $result);
    }

    public function testRegistrarDoc_Error()
    {
        // Configurar el mock para el método select en la verificación de existencia de documento
        $this->mockQuery->shouldReceive('select')
            ->andReturn([]); // Documento no existe

        // Configurar el mock para el método save en el registro de documento
        $this->mockQuery->shouldReceive('save')
            ->once()
            ->andReturn(0); // Error en la inserción

        $documento = '12345678A';

        $documentosModel = new DocumentosModel($this->mockQuery);
        $result = $documentosModel->registrarDoc($documento);

        $this->assertEquals('error', $result); // Verificar que se retorna 'error' cuando falla la inserción
    }

    public function testRegistrarDocDocumentoExistente()
    {
        // Documento que ya existe en la base de datos
        $documentoExistente = 'Documento A';

        // Configurar el mock para el método select en la consulta de verificar existencia
        $this->mockQuery->shouldReceive('select')
            ->with("SELECT * FROM documentos WHERE documento = '$documentoExistente'")
            ->andReturn(['id' => 1, 'documento' => $documentoExistente, 'estado' => 1]); // Simular que el documento ya existe

        // Instanciar DocumentosModel con el mock de Query
        $documentosModel = new DocumentosModel($this->mockQuery);

        // Llamar al método registrarDoc con el documento existente y almacenar el resultado
        $result = $documentosModel->registrarDoc($documentoExistente);

        // Verificar que el resultado sea "existe"
        $this->assertEquals('existe', $result);
    }


    public function testModificarDoc()
    {
        // Documento modificado y su ID
        $documento = 'Documento Modificado';
        $id = 1;

        // Configurar el mock para el método save en la consulta de actualización
        $this->mockQuery->shouldReceive('save')
            ->with("UPDATE documentos SET documento = ? WHERE id = ?", [$documento, $id])
            ->once()
            ->andReturn(1); // Éxito en la modificación

        // Instanciar DocumentosModel con el mock de Query
        $documentosModel = new DocumentosModel($this->mockQuery);

        // Llamar al método modificarDoc y almacenar el resultado
        $result = $documentosModel->modificarDoc($documento, $id);

        // Verificar que el resultado sea "modificado" (éxito en la modificación)
        $this->assertEquals('modificado', $result);
    }

    public function testModificarDocError()
    {
        // Documento y ID para la modificación
        $documento = 'Documento Modificado';
        $id = 1; // ID existente en la base de datos

        // Configurar el mock para el método save en la actualización del documento
        $this->mockQuery->shouldReceive('save')
            ->once() // Esperamos una sola llamada al método save
            ->with("UPDATE documentos SET documento = ? WHERE id = ?", [$documento, $id])
            ->andReturn(0); // Simular que la actualización del documento falló

        // Instanciar DocumentosModel con el mock de Query
        $documentosModel = new DocumentosModel($this->mockQuery);

        // Llamar al método modificarDoc y almacenar el resultado
        $result = $documentosModel->modificarDoc($documento, $id);

        // Verificar que el resultado sea "error"
        $this->assertEquals('error', $result);
    }




    public function testEditarDoc()
    {
        // ID del documento a editar
        $id = 1;

        // Datos simulados que se esperan retornar del método select
        $mockedData = ['id' => 1, 'documento' => 'Documento A', 'estado' => 1];

        // Configurar el mock para el método select en la consulta de editarDoc
        $this->mockQuery->shouldReceive('select')
            ->with("SELECT * FROM documentos WHERE id = $id")
            ->andReturn($mockedData);

        // Instanciar DocumentosModel con el mock de Query
        $documentosModel = new DocumentosModel($this->mockQuery);

        // Llamar al método editarDoc y almacenar el resultado
        $result = $documentosModel->editarDoc($id);

        // Verificar que el resultado retornado sea el mismo que $mockedData
        $this->assertEquals($mockedData, $result);
    }

    public function testAccionDoc()
    {
        // Estado y ID del documento a modificar
        $estado = 0;
        $id = 1;

        // Configurar el mock para el método save en la consulta de accionDoc
        $this->mockQuery->shouldReceive('save')
            ->with("UPDATE documentos SET estado = ? WHERE id = ?", [$estado, $id])
            ->once()
            ->andReturn(1); // Éxito en la actualización del estado

        // Instanciar DocumentosModel con el mock de Query
        $documentosModel = new DocumentosModel($this->mockQuery);

        // Llamar al método accionDoc y almacenar el resultado
        $result = $documentosModel->accionDoc($estado, $id);

        // Verificar que el resultado retornado sea 1 (éxito en la actualización del estado)
        $this->assertEquals(1, $result);
    }
}
