<?php

namespace Tests\Unit;

use App\Models\MajorModel;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as M;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../vendor/autoload.php';

class MajorModelTest extends TestCase
{
    protected $mockQuery;

    public function setUp(): void
    {
        parent::setUp();
        
        // Configurar el mock para el método select y insertar en el modelo MajorModel
        $this->mockQuery = M::mock('overload:App\Models\Query');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        M::close(); // Asegura que se cierren los mocks después de cada prueba
    }

    public function testVerify()
    {
        // Datos simulados que se esperan retornar del método select
        $item = 'correo';
        $valor = 'test@example.com';
        $mockedData = [['id' => 1, 'nombre' => 'Test Cliente', 'correo' => 'test@example.com', 'codphone' => '123', 'telefono' => '456', 'direccion' => 'Calle Principal', 'clave' => 'hashed_password']];

        // Configurar el mock para el método select en la consulta de verify
        $this->mockQuery->shouldReceive('select')
            ->with("SELECT * FROM clientes WHERE correo = 'test@example.com'")
            ->andReturn($mockedData);

        // Instanciar MajorModel con el mock de Query
        $majorModel = new MajorModel($this->mockQuery);

        // Llamar al método verify y almacenar el resultado
        $result = $majorModel->verify($item, $valor);

        // Verificar que el resultado retornado sea el mismo que $mockedData
        $this->assertEquals($mockedData, $result);
    }

    public function testRegister()
    {
        // Datos simulados para la inserción
        $nombre = 'Nuevo Cliente';
        $correo = 'nuevo@example.com';
        $codphone = '234';
        $telefono = '789';
        $direccion = 'Avenida Principal';
        $clave = 'hashed_password';

        // Configurar el mock para el método insertar en la consulta de register
        $this->mockQuery->shouldReceive('insertar')
            ->with(
                "INSERT INTO clientes (nombre, correo, codphone, telefono, direccion, clave) VALUES (?,?,?,?,?,?)",
                [$nombre, $correo, $codphone, $telefono, $direccion, $clave]
            )
            ->andReturn(true); // Supongamos que devuelve verdadero (éxito)

        // Instanciar MajorModel con el mock de Query
        $majorModel = new MajorModel($this->mockQuery);

        // Llamar al método register y almacenar el resultado
        $result = $majorModel->register($nombre, $correo, $codphone, $telefono, $direccion, $clave);

        // Verificar que el resultado retornado sea verdadero (éxito)
        $this->assertTrue($result);
    }
}

?>
