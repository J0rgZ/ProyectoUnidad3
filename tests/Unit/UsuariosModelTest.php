<?php

namespace Tests;

use App\Models\UsuariosModel;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as M;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../vendor/autoload.php'; 

class UsuariosModelTest extends TestCase
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

    public function testGetUsuario()
    {
        // Preparar los datos esperados
        $usuario = 'admin';
        $clave = 'password';
        $mockedData = [['id' => 1, 'usuario' => 'admin', 'nombre' => 'Administrador', 'correo' => 'admin@example.com', 'estado' => 1]];

        // Configurar el comportamiento esperado del mock de Query para el método select
        $this->mockQuery->shouldReceive('select')
            ->with("SELECT * FROM usuarios WHERE usuario = '$usuario' AND clave = '$clave'")
            ->once()
            ->andReturn($mockedData);

        // Instanciar el modelo y llamar al método que queremos probar
        $usuariosModel = new UsuariosModel();
        $result = $usuariosModel->getUsuario($usuario, $clave);

        // Asegurar que el resultado coincide con los datos esperados
        $this->assertEquals($mockedData, $result);
    }

    public function testRegistrarUsuarioExitoso()
    {
        // Datos simulados para el registro de un nuevo usuario
        $usuario = 'nuevo_usuario';
        $nombre = 'Nuevo Usuario';
        $correo = 'nuevo_usuario@example.com';
        $telefono = '123456789';
        $clave = 'password';

        // Configuración de mock para verificar si el usuario ya existe
        $this->mockQuery->shouldReceive('select')
            ->andReturn([]); // Simular que no existe ningún usuario con este nombre o correo

        // Configuración de mock para el método save en el registro de usuario
        $this->mockQuery->shouldReceive('save')
            ->andReturn(1); // Simular que la inserción fue exitosa

        // Instanciar UsuariosModel y llamar al método registrarUsuario
        $usuariosModel = new UsuariosModel();
        $result = $usuariosModel->registrarUsuario($usuario, $nombre, $correo, $telefono, $clave);

        // Verificar que el resultado sea "ok" (registro exitoso)
        $this->assertEquals('ok', $result);
    }

    public function testRegistrarUsuarioExistente()
    {
        // Datos simulados para el registro de un nuevo usuario
        $usuario = 'usuario_existente';
        $nombre = 'Usuario Existente';
        $correo = 'existente@example.com';
        $telefono = '987654321';
        $clave = 'password';

        // Configuración de mock para verificar si el usuario ya existe
        $this->mockQuery->shouldReceive('select')
            ->andReturn([['id' => 1]]); // Simular que el usuario ya existe en la base de datos

        // Instanciar UsuariosModel y llamar al método registrarUsuario
        $usuariosModel = new UsuariosModel();
        $result = $usuariosModel->registrarUsuario($usuario, $nombre, $correo, $telefono, $clave);

        // Verificar que el resultado sea "existe" (usuario ya registrado)
        $this->assertEquals('existe', $result);
    }

    public function testRegistrarUsuarioError()
    {
        // Datos simulados para el registro de un nuevo usuario
        $usuario = 'nuevo_usuario';
        $nombre = 'Nuevo Usuario';
        $correo = 'nuevo_usuario@example.com';
        $telefono = '123456789';
        $clave = 'password';

        // Configuración de mock para verificar si el usuario ya existe
        $this->mockQuery->shouldReceive('select')
            ->andReturn([]); // Simular que no existe ningún usuario con este nombre o correo

        // Configuración de mock para el método save en el registro de usuario
        $this->mockQuery->shouldReceive('save')
            ->andReturn(0); // Simular que la inserción falló

        // Instanciar UsuariosModel y llamar al método registrarUsuario
        $usuariosModel = new UsuariosModel();
        $result = $usuariosModel->registrarUsuario($usuario, $nombre, $correo, $telefono, $clave);

        // Verificar que el resultado sea "error" (registro fallido)
        $this->assertEquals('error', $result);
    }

    public function testGetUsuarios()
    {
        // Estado del usuario a buscar
        $estado = 1;

        // Datos simulados de usuarios que se esperan retornar
        $mockedData = [
            ['id' => 1, 'usuario' => 'user1', 'nombre' => 'Usuario 1', 'correo' => 'user1@example.com', 'estado' => 1],
            ['id' => 2, 'usuario' => 'user2', 'nombre' => 'Usuario 2', 'correo' => 'user2@example.com', 'estado' => 1],
        ];

        // Configuración de mock para el método selectAll en el modelo
        $sql = "SELECT id,usuario,nombre,correo,estado FROM usuarios WHERE estado = $estado";
        $this->mockQuery->shouldReceive('selectAll')
            ->with($sql)
            ->andReturn($mockedData);

        // Instanciar UsuariosModel y llamar al método getUsuarios
        $usuariosModel = new UsuariosModel();
        $result = $usuariosModel->getUsuarios($estado);

        // Verificar que el resultado coincida con los datos esperados
        $this->assertEquals($mockedData, $result);
    }

    public function testModificarUsuarioExitoso()
    {
        // Datos simulados para modificar un usuario existente
        $usuario = 'usuario_modificado';
        $nombre = 'Usuario Modificado';
        $correo = 'modificado@example.com';
        $telefono = '987654321';
        $idUsuario = 1;

        // Configuración de mock para el método save en la modificación de usuario
        $this->mockQuery->shouldReceive('save')
            ->andReturn(1); // Simular que la actualización fue exitosa

        // Instanciar UsuariosModel y llamar al método modificarUsuario
        $usuariosModel = new UsuariosModel();
        $result = $usuariosModel->modificarUsuario($usuario, $nombre, $correo, $telefono, $idUsuario);

        // Verificar que el resultado sea "modificado" (actualización exitosa)
        $this->assertEquals('modificado', $result);
    }

    public function testModificarUsuarioFallido()
    {
        // Datos simulados para modificar un usuario existente
        $usuario = 'usuario_modificado';
        $nombre = 'Usuario Modificado';
        $correo = 'modificado@example.com';
        $telefono = '987654321';
        $idUsuario = 999; // ID inexistente para forzar un fallo

        // Configuración de mock para el método save en la modificación de usuario
        $this->mockQuery->shouldReceive('save')
            ->andReturn(0); // Simular que la actualización falló

        // Instanciar UsuariosModel y llamar al método modificarUsuario
        $usuariosModel = new UsuariosModel();
        $result = $usuariosModel->modificarUsuario($usuario, $nombre, $correo, $telefono, $idUsuario);

        // Verificar que el resultado sea "error" (actualización fallida)
        $this->assertEquals('error', $result);
    }

    public function testEditarUser()
    {
        // ID del usuario a editar
        $userId = 1;

        // Datos simulados del usuario que se espera retornar
        $mockedUser = [
            'id' => 1,
            'usuario' => 'user1',
            'nombre' => 'Usuario 1',
            'correo' => 'user1@example.com',
            'telefono' => '123456789',
            'estado' => 1,
        ];

        // Configuración de mock para el método select en el modelo
        $sql = "SELECT * FROM usuarios WHERE id = $userId";
        $this->mockQuery->shouldReceive('select')
            ->with($sql)
            ->andReturn($mockedUser);

        // Instanciar UsuariosModel y llamar al método editarUser
        $usuariosModel = new UsuariosModel();
        $result = $usuariosModel->editarUser($userId);

        // Verificar que el resultado coincida con los datos esperados
        $this->assertEquals($mockedUser, $result);
    }

    public function testAccionUser()
    {
        // Estado y ID del usuario para cambiar
        $estado = 0; // Estado inactivo
        $idUsuario = 1;

        // Configuración de mock para el método save en la acción de usuario
        $this->mockQuery->shouldReceive('save')
            ->andReturn(1); // Simular que la actualización fue exitosa

        // Instanciar UsuariosModel y llamar al método accionUser
        $usuariosModel = new UsuariosModel();
        $result = $usuariosModel->accionUser($estado, $idUsuario);

        // Verificar que el resultado sea 1 (indicando éxito en la actualización)
        $this->assertEquals(1, $result);
    }

    public function testModificarPass()
    {
        // Nueva contraseña y ID del usuario
        $nuevaClave = 'nueva_password';
        $idUsuario = 1;

        // Configuración de mock para el método save en la modificación de contraseña
        $this->mockQuery->shouldReceive('save')
            ->andReturn(1); // Simular que la actualización fue exitosa

        // Instanciar UsuariosModel y llamar al método modificarPass
        $usuariosModel = new UsuariosModel();
        $result = $usuariosModel->modificarPass($nuevaClave, $idUsuario);

        // Verificar que el resultado sea 1 (indicando éxito en la actualización)
        $this->assertEquals(1, $result);
    }




    public function testGetEmpresa()
    {
        // Datos simulados que se esperan retornar del método select
        $mockedData = [['id' => 1, 'nombre' => 'Empresa A', 'direccion' => 'Calle Principal', 'telefono' => '123456789']];

        // Configurar el mock para el método select en la consulta de empresa
        $this->mockQuery->shouldReceive('select')
            ->with("SELECT * FROM configuracion")
            ->andReturn($mockedData);

        // Instanciar UsuariosModel y llamar al método getEmpresa
        $usuariosModel = new UsuariosModel();
        $result = $usuariosModel->getEmpresa();

        // Verificar que el resultado retornado sea el mismo que $mockedData
        $this->assertEquals($mockedData, $result);
    }

    public function testModificarDato()
    {
        // Datos simulados para modificar los datos de un usuario existente
        $usuario = 'usuario_modificado';
        $nombre = 'Usuario Modificado';
        $apellido = 'Apellido Modificado';
        $correo = 'modificado@example.com';
        $telefono = '987654321';
        $direccion = 'Calle Modificada';
        $perfil = 'imagen_modificada.jpg';
        $idUsuario = 1;

        // Configuración de mock para el método save en la modificación de datos de usuario
        $this->mockQuery->shouldReceive('save')
            ->andReturn(1); // Simular que la actualización fue exitosa

        // Instanciar UsuariosModel y llamar al método modificarDato
        $usuariosModel = new UsuariosModel();
        $result = $usuariosModel->modificarDato($usuario, $nombre, $apellido, $correo, $telefono, $direccion, $perfil, $idUsuario);

        // Verificar que el resultado sea 1 (indicando éxito en la actualización)
        $this->assertEquals(1, $result);
    }

    public function testModificarDatoFallido()
    {
        // Datos simulados para modificar los datos de un usuario existente
        $usuario = 'usuario_modificado';
        $nombre = 'Usuario Modificado';
        $apellido = 'Apellido Modificado';
        $correo = 'modificado@example.com';
        $telefono = '987654321';
        $direccion = 'Calle Modificada';
        $perfil = 'imagen_modificada.jpg';
        $idUsuario = 999; // ID inexistente para forzar un fallo

        // Configuración de mock para el método save en la modificación de datos de usuario
        $this->mockQuery->shouldReceive('save')
            ->andReturn(0); // Simular que la actualización falló

        // Instanciar UsuariosModel y llamar al método modificarDato
        $usuariosModel = new UsuariosModel();
        $result = $usuariosModel->modificarDato($usuario, $nombre, $apellido, $correo, $telefono, $direccion, $perfil, $idUsuario);

        // Verificar que el resultado sea 0 (indicando fallo en la actualización)
        $this->assertEquals(0, $result);
    }

}

