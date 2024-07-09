<?php

namespace Tests\Unit;

use App\Models\ContactModel;
use Mockery as M;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../vendor/autoload.php';

class ContactModelTest extends TestCase
{
    protected $mockQuery;

    public function setUp(): void
    {
        parent::setUp();
        
        // Configurar el mock para el método select en la consulta de empresa
        $this->mockQuery = M::mock('overload:App\Models\Query');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        M::close();
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

        // Instanciar ContactModel con el mock de Query
        $contactModel = new ContactModel($this->mockQuery);

        // Llamar al método getEmpresa y almacenar el resultado
        $result = $contactModel->getEmpresa();

        // Verificar que el resultado retornado sea el mismo que $mockedData
        $this->assertEquals($mockedData, $result);
    }
}