# 🤵 04: EL CONTROLADOR

Ubicación: `src/Controller/UserController.php`

El controlador es la cara pública de tu aplicación. Es el que recibe los `GET`, `POST` y `DELETE` de la gente de internet.

### Sus únicas responsabilidades:

1.  **Leer la petición:** Ver qué ID pide el usuario o qué JSON ha enviado.
2.  **Llamar al servicio:** Decirle "Oye, búscame al usuario 5".
3.  **Responder:** Enviar un JSON bonito y, lo más importante, el **Código de Estado HTTP** correcto.

### Los códigos de estado (No seas vago):

Un buen controlador no responde siempre con un `200 ok`.

- **200:** Todo genial.
- **201:** ¡He creado algo nuevo! (Ideal para el POST de registro).
- **404:** No he encontrado lo que buscabas.
- **400:** Me has enviado un JSON que da asco.
- **500:** He roto algo en el servidor, no me mires.

---

> [!IMPORTANT]
> Hay muchos más códigos de estado, pero estos son los más comunes.
> Si quieres saber más, busca en Google "Códigos de estado HTTP".
