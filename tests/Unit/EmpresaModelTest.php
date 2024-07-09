<?php

namespace Tests\Unit;

use App\Models\EmpresaModel;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as M;
use PHPUnit\Framework\TestCase;


require_once __DIR__ . '/../../vendor/autoload.php';


class EmpresaModelTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected $mockQuery;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockQuery = M::mock('overload:App\Models\Query'); // Mockear la clase Query utilizada en EmpresaModel
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
            'id' => 1,
            'ruc' => '123456789',
            'nombre' => 'Empresa A',
            'telefono' => '987654321',
            'correo' => 'empresa@example.com',
            'direccion' => 'Calle Principal',
            'mensaje' => 'Bienvenido a nuestra empresa',
            'logo' => 'logo.jpg',
        ];

        // Configurar el mock para el método select en la consulta de empresa
        $this->mockQuery->shouldReceive('select')
            ->once()
            ->with("SELECT * FROM configuracion")
            ->andReturn($mockedData);

        // Instanciar el modelo y llamar al método que queremos probar
        $empresaModel = new EmpresaModel($this->mockQuery);
        $result = $empresaModel->getEmpresa();

        // Verificar que el resultado retornado sea el mismo que $mockedData
        $this->assertEquals($mockedData, $result);
    }

    public function testModificar()
    {
        // Datos simulados para la modificación
        $ruc = '123456789';
        $nombre = 'Empresa B';
        $tel = '987654321';
        $correo = 'empresa@example.com';
        $dir = 'Calle Secundaria';
        $mensaje = 'Bienvenido a nuestra nueva empresa';
        $img = 'logo.jpg';
        $id = 1;

        // Configurar el mock para el método save en la actualización de configuracion
        $this->mockQuery->shouldReceive('save')
            ->once()
            ->with(
                "UPDATE configuracion SET ruc=?, nombre = ?, telefono =?, correo=?, direccion=?, mensaje=?, logo = ? WHERE id=?",
                [$ruc, $nombre, $tel, $correo, $dir, $mensaje, $img, $id]
            )
            ->andReturn(1); // Simular que la actualización fue exitosa

        // Instanciar el modelo y llamar al método que queremos probar
        $empresaModel = new EmpresaModel($this->mockQuery);
        $result = $empresaModel->modificar($ruc, $nombre, $tel, $correo, $dir, $mensaje, $img, $id);

        // Verificar que el resultado retornado sea "ok"
        $this->assertEquals('ok', $result);
    }

    public function testModificarError()
    {
        // Datos simulados para la modificación
        $ruc = '123456789';
        $nombre = 'Empresa B';
        $tel = '987654321';
        $correo = 'empresa@example.com';
        $dir = 'Calle Secundaria';
        $mensaje = 'Bienvenido a nuestra nueva empresa';
        $img = 'logo.jpg';
        $id = 1;

        // Configurar el mock para el método save en la actualización de configuracion
        $this->mockQuery->shouldReceive('save')
            ->once()
            ->with(
                "UPDATE configuracion SET ruc=?, nombre = ?, telefono =?, correo=?, direccion=?, mensaje=?, logo = ? WHERE id=?",
                [$ruc, $nombre, $tel, $correo, $dir, $mensaje, $img, $id]
            )
            ->andReturn(0); // Simular que la actualización falló

        // Instanciar el modelo y llamar al método que queremos probar
        $empresaModel = new EmpresaModel($this->mockQuery);
        $result = $empresaModel->modificar($ruc, $nombre, $tel, $correo, $dir, $mensaje, $img, $id);

        // Verificar que el resultado retornado sea "error"
        $this->assertEquals('error', $result);
    }


}
