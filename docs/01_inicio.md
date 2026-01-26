# 🚀 GUÍA PARA CÓMO NO EXPLOTAR EL BACKEND

¡Hola! Si estás aquí es porque quieres hacer una API en PHP pero que parezca profesional (o al menos como yo se hacer en **Spring Boot**) y no un espagueti de código.

### 🏠 ¿Cómo levanto esta cosa?

No te compliques. Abre una terminal en la carpeta raíz del proyecto y pega esto:

> [!IMPORTANT]
> Debes tener XAMPP instalado y haber hecho `cd` a la carpeta raíz del proyecto.

#### 1. Iniciar el Servidor de PHP

```powershell
C:\xampp\php\php.exe -S localhost:8000 -t public
```

#### 2. Iniciar MySQL

Despues activa la base de datos abre el panel de control de XAMPP.
Si no quieres usar el panel de control de XAMPP, puedes lanzarlo desde otra terminal con:

```powershell
C:\xampp\mysql\bin\mysqld.exe --console
```

#### 3. Crear la Base de Datos

Para crear la base de datos y las tablas iniciales:

1. Inicia **Apache** desde el Panel de Control de XAMPP (sí, aquí sí hace falta Apache porque phpMyAdmin es una web propia).
2. Entra en: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)

## Si el navegador te devuelve un JSON en `http://localhost:8000/api/users`, ¡felicidades! Sabes seguir instrucciones. 🥳

### 🏛️ La Arquitectura

Hemos dividido el código en capas para que no sea un lío:

1. **MODEL:** Solo dice qué datos tiene un objeto. (Ej: Un Usuario tiene nombre y email).

   1.5 **DTO:** Es un objeto que se usa para transportar datos entre capas. No tiene lógica.

2. **REPOSITORY:** Es el único que toca la Base de Datos. Si la base de datos muere, la has liado aquí seguro.
3. **SERVICE:** Aquí va la lógica (ej: validar cosas, transformar datos, etc).
4. **CONTROLLER:** El que recibe los pedidos (API) y entrega el JSON al cliente.

---

### 📂 ¿Qué hay en cada carpeta?

- `public/`: La puerta de entrada. Solo hay un `index.php` donde se define los endpoints. (En un futuro se añadiran una carpeta para las imagenes de las tarjetas)
- `src/`: Donde vive la magia.
- `docs/`: Lo que estás leyendo ahora mismo.

Mira los otros archivos en `docs/` para aprender a crear cosas nuevas sin romper nada. 👇
