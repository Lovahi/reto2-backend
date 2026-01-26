# 📋 00: EL PORQUÉ DE ESTA MOVIDA (5W)

Si te estás preguntando por qué nos hemos complicado la vida con tantas carpetas en vez de hacer un solo archivo `index.php` gigante, aquí tienes las respuestas.

### ❓ **What? (¿Qué es esto?)**

Es una arquitectura basada en **Capas** (N-Tier Architecture), muy similar a la que usa **Spring Boot**. Separamos la responsabilidad de cada trozo de código para que no sea un plato de espaguetis.

### ❓ **Why? (¿Por qué lo hacemos?)**

Porque no queremos llorar dentro de tres meses.

- **Mantenibilidad:** Si falla la base de datos, vas al Repository. Si el cálculo del IVA está mal, vas al Service. No tienes que buscar en 5000 líneas.
- **Seguridad:** Usar DTOs evita que envíes la contraseña del usuario a internet por accidente.
- **Escalabilidad:** Si mañana quieres cambiar MySQL por una API externa, solo cambias una capa, no toda la web.

### ❓ **Who? (¿Para quién?)**

Para desarrolladores que quieren pasar de "hacer webs que funcionan de milagro" a "crear software profesional". También para gente que viene de Java/Spring y no quiere sentir que PHP es retroceder al siglo pasado.

### ❓ **When? (¿Cuándo usarlo?)**

¡Siempre que el proyecto sea más grande que un "Hola Mundo"! Especialmente cuando trabajas en equipo y no quieres que el código de tu compañero te explote en la cara.

### ❓ **Where? (¿Dónde vive cada cosa?)**

Sigue los números de los documentos (01, 02, 02b...) y verás el camino que siguen los datos desde que entran por la URL hasta que salen por tu pantalla.
