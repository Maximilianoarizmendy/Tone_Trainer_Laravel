# 🏋️‍♂️ Documentación Técnica Completa: Tone Trainer

Esta documentación proporciona un desglose completo y detallado del proyecto **Tone Trainer**, una aplicación de entrenamiento, nutrición y seguimiento de metas deportivas desarrollada en **Laravel 11**.

---

## 🗺️ 1. Estructura del Proyecto

El sistema está estructurado bajo una arquitectura de separación de responsabilidades:
*   **Vistas e Interfaz (Frontend)**: Construido con **Laravel Blade** con estilos CSS premium y responsivos.
*   **Controladores Web (Mapeo de Vistas)**: Sirven las páginas HTML base al cliente.
*   **API REST Interna**: Todos los formularios y dinámicas del panel de control se procesan de manera asíncrona (AJAX con `fetch`) utilizando controladores API dedicados que devuelven respuestas en formato **JSON**.

---

## 💾 2. Diccionario de Datos y Modelos (`app/Models`)

El sistema cuenta con **10 modelos** principales que representan las entidades del negocio:

### 👤 `User.php`
Administra las identidades del sistema, perfiles, credenciales, métricas antropométricas iniciales y asignaciones de staff.
*   **Roles Definidos**:
    *   `ROLE_USER = 1` (Deportista / Cliente)
    *   `ROLE_ADMIN = 2` (Administrador global)
    *   `ROLE_NUTRITIONIST = 3` (Nutricionista asignado)
    *   `ROLE_TRAINER = 4` (Entrenador asignado)
*   **Relaciones**:
    *   `goals()`: `hasMany` con `Goal` (Metas personales).
    *   `achievements()`: `hasMany` con `Achievement` (Insignias ganadas).
    *   `sentMessages()` / `receivedMessages()`: `hasMany` con `Message` (Mensajes del chat).
    *   `nutritionPlans()`: `hasMany` con `NutritionPlan` (Comidas asignadas).
    *   `trainingPlans()`: `hasMany` con `TrainingPlan` (Ejercicios asignados).
    *   `workoutCalendar()`: `hasMany` con `WorkoutCalendar` (Calendario físico).
    *   `progressRecords()`: `hasMany` con `Progress` (Bitácora antropométrica).
    *   `preferences()`: `hasOne` con `UserPreference` (Ajustes de alertas).
    *   `nutritionist()` / `trainerUser()`: `belongsTo` a `User` (Staff asignado).

### 🎯 `Goal.php`
Representa los objetivos de rendimiento o cambio físico propuestos por el usuario.
*   **Campos clave**: `target_value` (valor objetivo), `current_value` (valor actual), `unit` (kg, %, reps, etc.), `status` (`active`, `completed`, `failed`).
*   **Propiedades calculadas**: `progress_percent` obtiene automáticamente el porcentaje de avance (de 0 a 100%).

### 🏋️ `TrainingPlan.php`
Rutinas de ejercicios personalizadas asignadas a los clientes por su entrenador.
*   **Campos clave**: `day_group` (ej. "Lunes - Pierna"), `exercise` (ej. "Sentadilla"), `series`, `reps`, `description` y `status`.

### ⏱️ `TrainingCompletion.php`
Bitácora de registro de cuándo el usuario completa un ejercicio individual de su plan diario de rutinas.
*   **Campos clave**: `completed_at` (fecha y hora exacta).

### 🥗 `NutritionPlan.php`
Define la pauta dietética asignada al usuario por día de la semana y tipo de comida.
*   **Campos clave**: `day_of_week` (Lunes-Domingo), `meal_type` (Desayuno, Almuerzo, etc.), `food_name`, y macronutrientes (`calories`, `protein`, `carbs`, `fats`).

### 📈 `Progress.php`
Registro histórico de medidas corporales y hábitos saludables del deportista.
*   **Campos clave**: `weight` (peso), `height` (altura), `body_fat` (% grasa), `muscle_mass` (% músculo), `bmi` (IMC), `water_intake` (agua diaria), `protein_intake` (proteína diaria) y notas.

### 📅 `WorkoutCalendar.php`
Calendario interactivo para que el usuario planifique y agende entrenamientos, registrando tiempo y gasto energético.
*   **Campos clave**: `workout_date` (fecha), `workout_type` (fuerza, cardio, etc.), `duration_minutes`, `calories_burned` y `completed`.

### 💬 `Message.php`
Entidad que permite la mensajería instantánea dentro de la plataforma entre los usuarios y el equipo técnico.
*   **Campos clave**: `sender_id` (remitente), `receiver_id` (destinatario), `message`, `is_read` (estado de lectura).

### 🏆 `Achievement.php`
Sistema de gamificación de logros e insignias ganadas por cumplir metas.
*   **Campos clave**: `badge_name` (Insignia), `badge_icon` (Emoji descriptivo), `description`.

### ⚙️ `UserPreference.php`
Ajustes y notificaciones personalizadas de los usuarios.
*   **Campos clave**: `training_level`, `weekly_frequency`, `physical_restrictions`, `preferred_schedule` y opciones booleanas para alertas de correo o notificaciones del sistema.

---

## 🔌 3. Controladores (`app/Http/Controllers`)

### 🌐 Controladores de Vistas (Web)

*   **`LandingController.php`**: Controla el renderizado de la página comercial de bienvenida pública (`/`).
*   **`DashboardController.php`**: El núcleo de navegación. Sirve las vistas Blade principales del panel y sincroniza en cada petición el rol activo del usuario en la sesión (`syncRole()`). Administra el listado de deportistas asignados en las pestañas del staff (`users()`, `admin()`, `trainer()`, `nutritionist()`).

### ⚡ Controladores de API (`app/Http/Controllers/Api`)
Retornan únicamente respuestas en formato **JSON**:

*   **`GoalController.php`**: Administra el CRUD de metas. Cuenta con un sistema de medallas (`unlockAchievement`): cuando una meta de categoría (`peso`, `musculo`, `grasa`, etc.) se marca como completada, se crea una medalla en la colección de logros del usuario.
*   **`WorkoutCalendarController.php`**: Controla las citas de entrenamiento en el calendario mensual y extrae estadísticas del progreso actual del mes.
*   **`MessageController.php`**: Carga conversaciones activas, hilos de chat con usuarios específicos, cuenta los mensajes sin leer y despacha nuevos mensajes.
*   **`NutritionPlanController.php`**: Administra los alimentos y comidas añadidas o asignadas en la dieta del usuario.
*   **`ProgressController.php`**: Inserta nuevas métricas en el historial del deportista y calcula automáticamente el Índice de Masa Corporal (IMC).
*   **`TrainingPlanController.php`**: Permite a entrenadores asignar o borrar ejercicios de la rutina de un cliente.
*   **`TrainingCompletionController.php`**: Marca ejercicios individuales del día como hechos o pendientes, actualizando estadísticas de asistencia y constancia.
*   **`ProfilePhotoController.php`**: Valida, sube y reemplaza de forma asíncrona la foto de perfil en el servidor.
*   **`SettingsController.php`**: Actualiza preferencias de cuenta y permite la auto-desactivación de la cuenta.
*   **`UserController.php`**: Utilizado por administradores y staff para el control de cuentas de usuarios de la plataforma.

---

## 🛣️ 4. Sistema de Enrutamiento (`routes/`)

### Vistas Web (`routes/web.php`)
```php
// Páginas públicas e invitados
Route::get('/', [LandingController::class, 'index']);
Route::middleware('guest')->group(function () {
    Route::get('/login', ...);
    Route::post('/login', ...);
    Route::get('/register', ...);
    Route::get('/forgot-password', ...);
});

// Panel Protegido
Route::middleware('auth')->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index']);          // Muro principal
    Route::get('/entrenamiento', [DashboardController::class, 'training']);
    Route::get('/nutricion', [DashboardController::class, 'nutrition']);
    Route::get('/mensajes', [DashboardController::class, 'messages']);
    Route::get('/perfil', [DashboardController::class, 'profile']);
    
    // Rutas Exclusivas por Roles
    Route::middleware('role:1')->group(function () {
        Route::get('/progreso', [DashboardController::class, 'progress']);
        Route::get('/metas', [DashboardController::class, 'goals']);
    });
    Route::middleware('role:2')->group(function () { Route::get('/admin', ...); });
    Route::middleware('role:3')->group(function () { Route::get('/nutricionista', ...); });
    Route::middleware('role:4')->group(function () { Route::get('/entrenador', ...); });
});
```

### Consultas Asíncronas API (`routes/api.php`)
Todas encapsuladas bajo el Middleware `auth`. Consumidas mediante JS `fetch()`:
*   `GET/POST/PUT/DELETE /api/calendar/workouts`
*   `GET/POST/PUT/DELETE /api/goals`
*   `GET /api/messages/conversations` y `POST /api/messages/send`
*   `POST /api/progress/metrics`
*   `POST /api/training/complete`

---

## 🔒 5. Sistema de Roles e Interacciones

El flujo de control de acceso se basa en el campo `role` de la tabla `users`:

```
[Usuario] ──> ¿Qué rol tiene en BD?
                │
                ├──> Rol 1 (Cliente)       ──> Secciones de progreso, metas y agenda.
                ├──> Rol 2 (Admin)         ──> Panel de control global de usuarios.
                ├──> Rol 3 (Nutricionista) ──> Gestión dietética de sus clientes asignados.
                └──> Rol 4 (Entrenador)    ──> Asignación de rutinas de ejercicios.
```

### Seguridad por Middleware
Se implementó un middleware dinámico de roles (`role:X`) que valida en tiempo real si el usuario autenticado tiene permitido el ingreso a secciones específicas de la plataforma. De no ser así, la aplicación responde con un error `403 Forbidden` (No autorizado), blindando la seguridad del sistema.
