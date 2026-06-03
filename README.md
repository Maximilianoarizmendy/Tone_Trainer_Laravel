# Tone Trainer - Plataforma Integral de Fitness

Tone Trainer es una plataforma integral desarrollada en Laravel para la gestión de rutinas, planes de nutrición, membresías y seguimiento de progreso físico tanto para gimnasios como para entrenadores personales.

## 📋 Características Principales

El sistema está diseñado para manejar 4 tipos de roles principales: **Administrador**, **Entrenador**, **Nutricionista** y **Usuario (Cliente)**.

- **💪 Gestión de Rutinas y Dietas:** Los entrenadores y nutricionistas pueden asignar planes personalizados a sus clientes.
- **📈 Seguimiento de Progreso:** Los usuarios pueden registrar su peso, IMC, masa muscular y recibir retroalimentación y validación de su entrenador.
- **🏆 Retos y Gamificación:** Los entrenadores pueden lanzar retos (mensuales/semanales). Los usuarios que los cumplen reciben insignias (medals) virtuales.
- **💬 Mensajería y Notificaciones:** Chat directo entre entrenador y usuario. Notificaciones In-App para avisos, recordatorios y cambios en los planes.
- **💳 Membresías y Pagos:** El administrador puede crear planes del gimnasio y registrar los pagos de los usuarios.
- **📅 Calendario de Entrenamientos:** Agendamiento de sesiones.
- **📊 Reportes y Ranking:** Tablas de clasificación basadas en logros y un panel analítico completo para el administrador.

## 🛠️ Tecnologías Utilizadas

- **Backend:** Laravel (PHP)
- **Base de Datos:** SQLite (para entorno local/desarrollo)
- **Frontend:** Blade Templates, CSS nativo, JavaScript
- **Autenticación:** Sesiones nativas de Laravel (encriptadas mediante Bcrypt)

## 🚀 Instalación y Configuración

1.  **Clonar y Dependencias:**
    Clona el repositorio y ejecuta la instalación de los paquetes:
    ```bash
    composer install
    ```
2.  **Entorno:**
    Copia el archivo `.env.example` a `.env` (si no existe) y genera la clave de aplicación:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
3.  **Base de Datos:**
    Crea un archivo vacío llamado `database.sqlite` dentro de la carpeta `database/`. Luego corre las migraciones para estructurar todas las tablas:
    ```bash
    php artisan migrate
    ```
4.  **Ejecución:**
    Levanta el servidor local de desarrollo:
    ```bash
    php artisan serve
    ```
    Visita `http://localhost:8000` en tu navegador.

## 📚 Documentación de Código

Todo el código fuente backend, incluyendo los **Modelos** y los **Controladores**, se encuentra documentado siguiendo el estándar `PHPDoc` en español. 

- Los **Modelos** (`app/Models`) contienen comentarios sobre las tablas que administran y las relaciones que componen.
- Los **Controladores API** (`app/Http/Controllers/Api/`) incluyen la documentación de la lógica de negocio, reportes y permisos manejados para cada módulo.

---

Desarrollado para maximizar el rendimiento y disciplina en los entornos de entrenamiento personal y gimnasios de alto rendimiento.
