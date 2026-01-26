# 📦 01: EL MODELO (La Entidad)

Ubicación: `src/Model/User.php`

### ¿Qué es esto realmente?

El Modelo es el **reflejo de tu base de datos** en código. Si en tu tabla `users` tienes una columna `username`, en tu clase PHP debes tener una variable `$username`.

### ¿Por qué molestarse en crear una clase?

Podríamos usar arrays asociativos, pero queremos ser profesionales. Una clase te da:

- **Tipado:** Si dices que el ID es un `int`, nadie te puede colar un texto "Patata".
- **Orden:** Sabes exactamente qué datos tiene un usuario sin tener que abrir el phpMyAdmin.
- **Encapsulamiento:** Usamos `private` para que nadie pueda cambiarle el email a un usuario sin pasar por tus reglas.

### 💡 El constructor y los métodos

- **Constructor:** Es como el formulario de alta. Obligas a que, para crear un usuario, te den todos sus datos.
- **Getters:** Como las variables son privadas, necesitas "ventanas" para leerlas desde fuera.
- **toArray():** Es el traductor. Convierte el objeto complejo en algo que PHP sepa convertir a JSON.
