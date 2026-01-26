# 📦 01b: EL DTO (Data Transfer Object)

Ubicación: `src/DTO/UserDTO.php`

### ¿Por qué lo necesito?

Usamos DTOs para no enviar datos sensibles (como la contraseña) al frontend.

- **User (Model):** Contiene TODO (id, username, email, **password**). Es lo que se guarda en la DB.
- **UserDTO:** Contiene solo lo que el frontend necesita ver (id, username, email). **¡Cero contraseñas!**

### ¿Cómo funciona el flujo?

#### Al hacer un get de un usuario

1. El **Repository** saca el `User` (objeto real) de la base de datos.
2. El **Service** recibe ese `User` y lo transforma en un `UserDTO`.
3. El **Controller** envía el `UserDTO` al frontend.

#### Al hacer un post de un usuario

1. El **Controller** recibe el `UserDTO` del frontend.
2. El **Service** recibe ese `UserDTO` y lo transforma en un `User`.
3. El **Repository** inserta el `User` en la base de datos.
