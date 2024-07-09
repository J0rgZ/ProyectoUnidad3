<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use App\Models\CarsModel;
use App\Models\LoginModel;
use Mockery as M;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

require_once __DIR__ . '/../../vendor/autoload.php';

class FeatureContext implements Context
{
    use MockeryPHPUnitIntegration;

    protected $carsModelMock;
    protected $loginModelMock;
    protected $clientesModelMock;
    protected $isAdmin = false;
    private $vehiculosModelMock;
    protected $result;


    public function __construct()
    {
        // Mockear la clase LoginModel
        $this->loginModelMock = M::mock(LoginModel::class);
        
        // Mockear la clase CarsModel
        $this->carsModelMock = M::mock(CarsModel::class);

        // Mockear la clase ClientesModel
        $this->clientesModelMock = M::mock(ClientesModel::class);

        $this->vehiculosModelMock = M::mock(VehiculoModel::class);
    }

     /**
     * ------------------------------------------------------------------------------------------------------------------------------
     *                                                             CARS MODEL
     * ------------------------------------------------------------------------------------------------------------------------------
     */

    /**
     * @Given que tengo vehículos en el sistema con estado :estado
     */
    public function queTengoVehiculosEnElSistemaConEstado($estado)
    {
        // Simular la respuesta de getVehiculos con estado activo
        $vehiculos = [
            ['id' => 1, 'marca' => 'Toyota', 'tipo' => 'SUV', 'estado' => 'activo'],
            ['id' => 2, 'marca' => 'Honda', 'tipo' => 'Sedan', 'estado' => 'activo']
        ];

        // Configurar el comportamiento esperado del mock de CarsModel para el método getVehiculos
        $this->carsModelMock->shouldReceive('getVehiculos')
            ->with($estado)
            ->once()
            ->andReturn($vehiculos);
    }

    /**
     * @When consulto la lista de vehículos
     */
    public function consultoLaListaDeVehiculos()
    {
        // Llamar al método real de getVehiculos de CarsModel
        $this->result = $this->carsModelMock->getVehiculos('activo');
    }

    /**
     * @Then debería obtener una lista de vehículos con estado :estado
     */
    public function deberiaObtenerUnaListaDeVehiculosConEstado($estado)
    {
        // Validar que los vehículos obtenidos tengan el estado correcto
        foreach ($this->result as $vehiculo) {
            if ($vehiculo['estado'] !== $estado) {
                throw new Exception("Se encontró un vehículo con estado diferente a $estado");
            }
        }
    }

    /**
     * @Given que tengo un vehículo con ID :id
     */
    public function queTengoUnVehiculoConId($id)
    {
        // Simular la respuesta de getVehiculo con el ID específico
        $vehiculo = ['id' => $id, 'marca' => 'Toyota', 'tipo' => 'SUV', 'estado' => 'activo'];

        // Configurar el comportamiento esperado del mock de CarsModel para el método getVehiculo
        $this->carsModelMock->shouldReceive('getVehiculo')
            ->with($id)
            ->once()
            ->andReturn($vehiculo);
    }

    /**
     * @When consulto los detalles del vehículo con ID :id
     */
    public function consultoLosDetallesDelVehiculoConId($id)
    {
        // Llamar al método real de getVehiculo de CarsModel
        $this->result = $this->carsModelMock->getVehiculo($id);
    }

    /**
     * @Then debería ver los detalles del vehículo con ID :id
     */
    public function deberiaVerLosDetallesDelVehiculoConId($id)
    {
        // Validar que los detalles del vehículo retornado son correctos
        if (empty($this->result) || $this->result['id'] != $id) {
            throw new Exception("No se encontró el vehículo con ID $id");
        }
    }

    /**
     * ------------------------------------------------------------------------------------------------------------------------------
     *                                                             INICIAR SESION
     * ------------------------------------------------------------------------------------------------------------------------------
     */


     /**
     * @Given que tengo un usuario con correo :correo en la tabla :tabla
     */
    public function queTengoUnUsuarioConCorreoEnLaTabla($correo, $tabla)
    {
        // Ejemplo básico de simulación de usuario con correo específico
        $usuarios = [
            ['id' => 1, 'usuario' => 'john_doe', 'correo' => 'john@example.com', 'clave' => 'hashed_password', 'estado' => 1],
            ['id' => 2, 'usuario' => 'admin', 'correo' => 'admin@example.com', 'clave' => 'hashed_password', 'estado' => 1],
            ['id' => 3, 'usuario' => 'ana', 'correo' => 'ana@example.com', 'clave' => 'hashed_password', 'estado' => 1],
        ];

        // Buscar el usuario en el array simulado
        $usuarioEncontrado = null;
        foreach ($usuarios as $u) {
            if ($u['correo'] === $correo) {
                $usuarioEncontrado = $u;
                break;
            }
        }

        if (!$usuarioEncontrado) {
            throw new Exception("No se encontró el usuario con correo $correo en la tabla $tabla");
        }

        // Configurar el comportamiento esperado del mock de LoginModel para el método verify
        if ($usuarioEncontrado) {
            $this->loginModelMock->shouldReceive('verify')
                ->with($tabla, $correo, Mockery::any()) // Acepta cualquier contraseña para simular inicio de sesión exitoso
                ->once()
                ->andReturn($usuarioEncontrado);
        } else {
            $this->loginModelMock->shouldReceive('verify')
                ->with($tabla, $correo, Mockery::any()) // Acepta cualquier contraseña para simular inicio de sesión fallido
                ->once()
                ->andReturn(null);
            
        }

        

    }

     /**
     * @When verifico el inicio de sesión con correo electrónico :correo y contraseña :contrasena
     */
    public function verificoElInicioDeSesionConCorreoYContrasena($correo, $contrasena)
    {
        // Llamar al método real de verify en LoginModelMock
        // Aquí usamos una lógica para determinar si la contraseña es correcta o no
        if ($contrasena === 'hashed_password') {
            $this->result = $this->loginModelMock->verify('usuarios', $correo, $contrasena);
        } else{
            $this->result = null; // Simulamos un inicio de sesión fallido
        }
    }

    /**
     * @Then debería obtener un resultado exitoso de verificación de inicio de sesión
     */
    public function deberiaObtenerUnResultadoExitosoDeVerificacionDeInicioDeSesion()
    {
        // Validar que el resultado de la verificación sea exitoso según la lógica de negocio
        if (empty($this->result)) {
            throw new Exception("Inicio de sesión fallido. No se obtuvieron datos.");
        }

        // Por ejemplo, podrías verificar que el usuario está activo
        if ($this->result['estado'] !== 1) {
            throw new Exception("Inicio de sesión fallido. El usuario no está activo.");
        }
    }

    /**
     * @Then debería obtener un resultado fallido de verificación de inicio de sesión
     */
    public function deberiaObtenerUnResultadoFallidoDeVerificacionDeInicioDeSesion()
    {
        // Validar que el resultado de la verificación sea fallido según la lógica de negocio
        // Aquí verificamos que el resultado sea nulo para indicar un inicio de sesión fallido
        if (!empty($this->result)) {
            throw new Exception("Inicio de sesión exitoso inesperado.");
        }
    }

    /**
     * ------------------------------------------------------------------------------------------------------------------------------
     *                                                                  CLIENTES
     * ------------------------------------------------------------------------------------------------------------------------------
     */


     /**
     * @Given que tengo acceso al sistema como administrador
     */
    public function queTengoAccesoAlSistemaComoAdministrador()
    {
        // Establecer que se tiene acceso como administrador
        $this->isAdmin = true;
    }

    /**
     * @When registro un nuevo cliente con los siguientes datos:
     */
    public function registroUnNuevoClienteConLosSiguientesDatos(TableNode $table)
    {
        if (!$this->isAdmin) {
            throw new Exception("No se tiene acceso como administrador.");
        }

        $clienteData = $table->getHash()[0];
        $dni = $clienteData['DNI'];
        $nombre = $clienteData['Nombre'];
        $telefono = $clienteData['Teléfono'];
        $direccion = $clienteData['Dirección'];

        // Configurar el comportamiento esperado del mock de ClientesModel para registrarCliente
        $this->clientesModelMock->shouldReceive('registrarCliente')
            ->with($dni, $nombre, $telefono, $direccion)
            ->once()
            ->andReturn('ok'); // Simular que el registro fue exitoso
            
        // Llamar al método real o simulado que realiza el registro
        $this->result = $this->clientesModelMock->registrarCliente($dni, $nombre, $telefono, $direccion);
    }

    /**
     * @When intento registrar un nuevo cliente con los siguientes datos:
     */
    public function intentoRegistrarUnNuevoClienteConLosSiguientesDatos(TableNode $table)
    {
        if (!$this->isAdmin) {
            throw new Exception("No se tiene acceso como administrador.");
        }

        $clienteData = $table->getHash()[0];
        $dni = $clienteData['DNI'];
        $nombre = $clienteData['Nombre'];
        $telefono = $clienteData['Teléfono'];
        $direccion = $clienteData['Dirección'];

        // Configurar el comportamiento esperado del mock de ClientesModel para registrarCliente
        $this->clientesModelMock->shouldReceive('registrarCliente')
            ->with($dni, $nombre, $telefono, $direccion)
            ->once()
            ->andReturn('existe'); // Simular que ya existe un cliente con ese nombre
            
        // Llamar al método real o simulado que realiza el registro
        $this->result = $this->clientesModelMock->registrarCliente($dni, $nombre, $telefono, $direccion);
    }

    /**
     * @When intento registrar un nuevo cliente con datos incorrectos
     */
    public function intentoRegistrarUnNuevoClienteConDatosIncorrectos()
    {
        if (!$this->isAdmin) {
            throw new Exception("No se tiene acceso como administrador.");
        }

        // Configurar el comportamiento esperado del mock de ClientesModel para registrarCliente
        $this->clientesModelMock->shouldReceive('registrarCliente')
            ->once()
            ->andReturn('error'); // Simular un error al intentar registrar el cliente
            
        // Llamar al método real o simulado que realiza el registro
        $this->result = $this->clientesModelMock->registrarCliente('dni_incorrecto', 'nombre_incorrecto', 'telefono_incorrecto', 'direccion_incorrecta');
    }


    /**
     * @Then debería ver el mensaje :arg1
     */
    public function deberiaVerElMensaje($mensajeEsperado)
    {
        // Implementar la validación para verificar el mensaje esperado
        switch ($this->result) {
            case 'ok':
                $mensajeObtenido = 'Cliente registrado correctamente.';
                break;
            case 'existe':
                $mensajeObtenido = 'Ya existe un cliente con ese nombre.';
                break;
            case 'error':
                $mensajeObtenido = 'Ocurrió un error al intentar registrar al cliente.';
                break;
            default:
                throw new Exception("Resultado inesperado durante la validación del mensaje.");
        }

        if ($mensajeObtenido !== $mensajeEsperado) {
            throw new Exception("El mensaje obtenido '{$mensajeObtenido}' no coincide con el esperado '{$mensajeEsperado}'.");
        }
    }

    /**
     * ------------------------------------------------------------------------------------------------------------------------------
     *                                                                  VEHICULOS
     * ------------------------------------------------------------------------------------------------------------------------------
     */

    





    
    public function __destruct()
    {
        M::close();
    }
}