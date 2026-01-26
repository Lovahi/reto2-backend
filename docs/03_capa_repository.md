# 🗄️ 02: EL REPOSITORIO

Ubicación: `src/Repository/TuObjetoRepository.php`

### ¿Qué es esto?

Es el **único** tío que tiene permiso para hablar con la base de datos. Nadie más puede hacer un `SELECT`, un `INSERT` o algo otra consulta sql.

### ¿Qué hace?

- Busca cosas por id, nombre, email, etc.
- Trae la lista completa de la tabla.
- Guarda (hace el `INSERT` o `UPDATE`).
- Borra registros.

---

> [!WARNING]
> Mantén tus SQLs siempre dentro del Repository. Nunca en el Service y MUCHO MENOS en el Controller.
