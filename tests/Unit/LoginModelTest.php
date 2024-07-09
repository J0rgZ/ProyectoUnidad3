<?php

namespace Tests\Unit;

use App\Models\LoginModel;
use Mockery as M;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../vendor/autoload.php';

class LoginModelTest extends TestCase
{
    protected $mockQuery;

    public function setUp(): void
    {
        parent::setUp();
        
        // Configurar el mock para el método select en el modelo LoginModel
        $this->mockQuery = M::mock('overload:App\Models\Query');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        M::close();
    }

    public function testVerifyWithOtherTable()
    {
        // Datos simulados que se esperan retornar del método select
        $mockedData = [['id' => 1, 'correo' => 'admin@example.com', 'clave' => 'hashed_password', 'estado' => 1]];

        // Configurar el mock para el método select en la consulta de verify
        $this->mockQuery->shouldReceive('select')
            ->with("SELECT * FROM otra_tabla WHERE correo = 'admin@example.com' AND clave = 'hashed_password' AND estado = 1")
            ->andReturn($mockedData);

        // Instanciar LoginModel con el mock de Query
        $loginModel = new LoginModel($this->mockQuery);

        // Llamar al método verify y almacenar el resultado
        $result = $loginModel->verify('otra_tabla', 'admin@example.com', 'hashed_password');

        // Verificar que el resultado retornado sea el mismo que $mockedData
        $this->assertEquals($mockedData, $result);
    }
    
}
?>



