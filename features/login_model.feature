Feature: Verificación de inicio de sesión en el sistema

  Scenario: Verificar inicio de sesión exitoso con correo electrónico en tabla de usuarios
    Given que tengo un usuario con correo "john@example.com" en la tabla "usuarios"
    When verifico el inicio de sesión con correo electrónico "john@example.com" y contraseña "hashed_password"
    Then debería obtener un resultado exitoso de verificación de inicio de sesión

  Scenario: Verificar inicio de sesión exitoso con correo electrónico en tabla de usuarios
    Given que tengo un usuario con correo "admin@example.com" en la tabla "usuarios"
    When verifico el inicio de sesión con correo electrónico "admin@example.com" y contraseña "hashed_password"
    Then debería obtener un resultado exitoso de verificación de inicio de sesión
