# 🧠 03: EL SERVICIO

Ubicación: `src/Service/UserService.php`

Esta es, con diferencia, la capa más importante aunque parezca la más simple. Es el puente entre el "Qué quiero" y el "Cómo lo guardo".

### ¿Qué hace un Servicio?

- **Orquestación:** Llama al Repositorio para traer datos y luego los transforma a DTO.
- **Lógica de Negocio:** Imagina que un usuario solo se puede registrar si es mayor de 18 años. Esa comprobación NO va en el controlador ni en la base de datos. Va aquí.
- **Transformación:** Es el lugar donde ocurre la magia de `User -> UserDTO`.

### ¿Por qué lo necesitamos?

Si pusiéramos la lógica en el controlador, y mañana queremos hacer un comando de terminal para registrar usuarios masivamente, tendríamos que copiar y pegar todo el código.
Al tenerlo en un **Service**, el Controlador de la API y el Comando de Terminal pueden compartir el mismo "cerebro".

### Ejemplo de flujo en el Service:

```php
public function getUser(int $id): ?UserDTO {
    // 1. Pido el usuario real (Entity) al Repo
    $user = $this->userRepository->findById($id);

    // 2. Si no existe, devuelvo null y el controlador ya mandará un 404
    if (!$user) return null;

    // 3. Lo convierto en DTO (le quito la contraseña y datos feos)
    return new UserDTO($user->getId(), $user->getUsername(), $user->getEmail());
}
```
