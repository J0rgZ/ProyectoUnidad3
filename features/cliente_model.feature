Feature: Registro de cliente
  Verificar el proceso de registro de un nuevo cliente en el sistema.

  Scenario: Registro exitoso de un nuevo cliente
    Given que tengo acceso al sistema como administrador
    When registro un nuevo cliente con los siguientes datos:
      | DNI        | Nombre         | Teléfono     | Dirección              |
      | 12345678A  | Juan Pérez     | 987654321    | Calle Principal, 123   |
    Then debería ver el mensaje "Cliente registrado correctamente."

  Scenario: Intento de registro de un cliente que ya existe
    Given que tengo acceso al sistema como administrador
    When intento registrar un nuevo cliente con los siguientes datos:
      | DNI        | Nombre      | Teléfono    | Dirección              |
      | 12345678A  | Juan Pérez  | 987654321   | Calle Principal, 123   |
    Then debería ver el mensaje "Ya existe un cliente con ese nombre."

  Scenario: Falla en el registro de un nuevo cliente
    Given que tengo acceso al sistema como administrador
    When intento registrar un nuevo cliente con datos incorrectos
    Then debería ver el mensaje "Ocurrió un error al intentar registrar al cliente."
