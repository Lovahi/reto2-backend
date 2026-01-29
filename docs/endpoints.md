# 🚀 Endpoints de la API

## 🔐 Autenticación

### `POST` /api/auth/register

> **Registro de nuevos usuarios.**

- **Cuerpo (JSON):**
  ```json
  {
    "username": "usuario123",
    "email": "user@example.com",
    "password": "mi_password"
  }
  ```
- **Respuesta (201):** `{"message": "User registered successfully"}`

---

### `POST` /api/auth/login

> **Inicio de sesión y creación de sesión en el servidor.**

- **Cuerpo (JSON):**
  ```json
  {
    "email": "user@example.com",
    "password": "mi_password"
  }
  ```
- **Respuesta (200):**
  ```json
  {
    "id": 1,
    "username": "usuario123",
    "email": "user@example.com",
    "role": "USER"
  }
  ```

---

### `POST` /api/auth/logout

> **Cierre de sesión. Requiere estar autenticado.**

- **Respuesta (200):** `{"message": "Logged out successfully"}`

---

## 👤 Usuarios

### `GET` /api/users

> **Lista todos los usuarios (Solo ADMIN suele tener acceso total en lógica de negocio).**

---

### `GET` /api/users/{id}

> **Obtiene los detalles de un usuario específico. Requiere Auth.**

---

## 🎮 Juegos

### `GET` /api/games

> **Listado de juegos con soporte para filtros dinámicos y paginación.**

- **Parámetros URL (Opcionales):**
  - `page`: Número de página (default: 1)
  - `titulo`: Búsqueda por nombre parcial.
  - `genero`: Filtrado por categoría.
  - `plataforma`: Filtrado por sistema.

---

### `GET` /api/games/pages

> **Calcula el total de páginas disponibles según los filtros aplicados.**

- **Respuesta (200):** `{"total": 5}`

---

### `GET` /api/games/{id}

> **Obtiene la ficha técnica de un juego por su ID.**

---

## 📅 Eventos

### `GET` /api/events

> **Lista los eventos (charlas, torneos, talleres) con filtros y paginación.**

---

### `GET` /api/events/pages

> **Total de páginas de eventos según filtros.**

---

### `POST` /api/events/{id}/signup

> **Inscripción del usuario actual en un evento.**
> _Disminuye automáticamente el aforo en -1._

---

### `DELETE` /api/events/{id}/signup

> **Cancela la inscripción en un evento.**
> _Aumenta automáticamente el aforo en +1._

---

## 🛠️ Administración

### `POST` /api/events

> **Creación de nuevos eventos. Solo ADMIN.**

- **Datos:** Permite envío de imágenes mediante `multipart/form-data`.
- **Campos:** `title`, `type`, `date`, `hour`, `availablePlaces`, `image`, `description`.

---

### `PUT` /api/users/{id}

> **Actualización de datos de usuario. Solo ADMIN.**

---

### `DELETE` /api/users/{id}

> **Eliminación de un usuario. Solo ADMIN.**
