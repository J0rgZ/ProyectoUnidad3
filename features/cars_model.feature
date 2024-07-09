Feature: Consulta de vehículos y detalles de vehículo en el sistema

  Scenario: Consultar lista de vehículos activos
    Given que tengo vehículos en el sistema con estado "activo"
    When consulto la lista de vehículos
    Then debería obtener una lista de vehículos con estado "activo"

  Scenario: Consultar detalles de un vehículo específico
    Given que tengo un vehículo con ID "1"
    When consulto los detalles del vehículo con ID "1"
    Then debería ver los detalles del vehículo con ID "1"
