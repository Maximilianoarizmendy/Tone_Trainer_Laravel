-- PostgreSQL dump para tone_trainer_db
-- Convertido desde MySQL/MariaDB

BEGIN;

-- ── Users ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role SMALLINT NOT NULL DEFAULT 2,
    nutritionist_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
    trainer_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    birthdate DATE,
    profile_photo VARCHAR(255),
    reset_token VARCHAR(255),
    reset_expires TIMESTAMP,
    medical_history VARCHAR(255),
    phone VARCHAR(20),
    location VARCHAR(100),
    goal VARCHAR(255),
    level VARCHAR(50),
    weight NUMERIC(5,2),
    height NUMERIC(5,2),
    imc NUMERIC(5,2),
    trainer VARCHAR(100),
    membership_start DATE,
    nutrition_plan TEXT,
    achievements TEXT,
    email_verified_at TIMESTAMP,
    remember_token VARCHAR(100)
);

-- ── Exercises ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS exercises (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100),
    description TEXT,
    type VARCHAR(50),
    video_url TEXT,
    last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── Training Plan ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS training_plan (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    assigned_by INTEGER NOT NULL,
    day_group VARCHAR(50) NOT NULL,
    exercise VARCHAR(100) NOT NULL,
    series INTEGER NOT NULL,
    reps INTEGER NOT NULL,
    description TEXT,
    status SMALLINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── Training Completions ────────────────────────────────
CREATE TABLE IF NOT EXISTS training_completions (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    exercise_id INTEGER NOT NULL REFERENCES training_plan(id) ON DELETE CASCADE,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_tc_user_date ON training_completions(user_id, completed_at);
CREATE INDEX IF NOT EXISTS idx_tc_exercise ON training_completions(exercise_id);

-- ── Nutrition Plan ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS nutrition_plan (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    day_of_week VARCHAR(20),
    meal_type VARCHAR(20),
    food_name VARCHAR(100),
    calories INTEGER,
    protein INTEGER,
    carbs INTEGER,
    fats INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── Progress ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS progress (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    weight NUMERIC(5,2),
    body_fat NUMERIC(5,2),
    muscle_mass NUMERIC(5,2),
    bmi NUMERIC(5,2),
    water_intake NUMERIC(5,2),
    protein_intake NUMERIC(6,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── Goals ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS goals (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(50) NOT NULL,
    target_value NUMERIC(10,2) NOT NULL,
    current_value NUMERIC(10,2) DEFAULT 0,
    unit VARCHAR(20),
    deadline DATE,
    status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP
);

-- ── Achievements ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS achievements (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    badge_name VARCHAR(100) NOT NULL,
    badge_icon VARCHAR(50),
    description TEXT,
    earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── Messages ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS messages (
    id SERIAL PRIMARY KEY,
    sender_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    receiver_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    message TEXT NOT NULL,
    is_read SMALLINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_conversation ON messages(sender_id, receiver_id);
CREATE INDEX IF NOT EXISTS idx_receiver_unread ON messages(receiver_id, is_read);
CREATE INDEX IF NOT EXISTS idx_created ON messages(created_at);

-- ── Notifications ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS notifications (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    from_user_id INTEGER,
    type VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    is_read SMALLINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── Workout Calendar ────────────────────────────────────
CREATE TABLE IF NOT EXISTS workout_calendar (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    workout_date DATE NOT NULL,
    workout_type VARCHAR(50),
    title VARCHAR(255),
    notes TEXT,
    completed SMALLINT DEFAULT 0,
    duration_minutes INTEGER,
    calories_burned INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE(user_id, workout_date, title)
);

-- ── Routines ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS routines (
    id SERIAL PRIMARY KEY,
    trainer_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
    name VARCHAR(100),
    objective VARCHAR(100),
    level VARCHAR(20),
    description TEXT,
    duration_minutes SMALLINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── Routine Exercises ───────────────────────────────────
CREATE TABLE IF NOT EXISTS routine_exercises (
    id SERIAL PRIMARY KEY,
    routine_id INTEGER REFERENCES routines(id) ON DELETE CASCADE,
    exercise_id INTEGER REFERENCES exercises(id) ON DELETE CASCADE,
    sets SMALLINT,
    reps SMALLINT,
    rest_seconds SMALLINT,
    last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── Historial Routines ──────────────────────────────────
CREATE TABLE IF NOT EXISTS historial_routines (
    id_historial SERIAL PRIMARY KEY,
    routine_id INTEGER,
    name_old VARCHAR(100),
    modification_date TIMESTAMP
);

-- ── Other tables ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS audit_log (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
    action VARCHAR(100),
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    details TEXT,
    last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS backup_users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role VARCHAR(20) DEFAULT 'user',
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    birthdate DATE,
    medical_history VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS company_preferences (
    id SERIAL PRIMARY KEY,
    company_name VARCHAR(100),
    goals TEXT,
    contact_email VARCHAR(100),
    notification_settings TEXT,
    last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS events (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL
);

CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS nutrition_plans (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    goal VARCHAR(100),
    description TEXT,
    daily_calories INTEGER,
    start_date DATE,
    end_date DATE,
    last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS physical_tracking (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    date DATE,
    weight_kg NUMERIC(5,2),
    body_fat_percentage NUMERIC(4,2),
    measurements TEXT,
    notes TEXT,
    last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    "IMC" DOUBLE PRECISION
);

CREATE TABLE IF NOT EXISTS progress_metrics (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    date DATE NOT NULL DEFAULT CURRENT_DATE,
    weight NUMERIC(5,2),
    height NUMERIC(5,2),
    imc NUMERIC(5,2),
    body_fat NUMERIC(5,2),
    muscle_mass NUMERIC(5,2),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    bmi NUMERIC(5,2),
    water_intake NUMERIC(5,2),
    protein_intake NUMERIC(6,2)
);

CREATE TABLE IF NOT EXISTS traceability (
    id SERIAL PRIMARY KEY,
    requirement_id VARCHAR(50),
    linked_module VARCHAR(100),
    status VARCHAR(50),
    updated_by VARCHAR(100),
    last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS user_behavior (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    action VARCHAR(100),
    "timestamp" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    context TEXT,
    last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS user_preferences (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    goal VARCHAR(100),
    training_level VARCHAR(20),
    weekly_frequency SMALLINT,
    training_type VARCHAR(100),
    physical_restrictions TEXT,
    preferred_schedule VARCHAR(20),
    reminders BOOLEAN,
    push_notifications BOOLEAN,
    last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── Triggers (PostgreSQL syntax) ────────────────────────
CREATE OR REPLACE FUNCTION guardar_historial_routine() RETURNS TRIGGER AS $$
BEGIN
    INSERT INTO historial_routines (routine_id, name_old, modification_date)
    VALUES (OLD.id, OLD.name, NOW());
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_guardar_historial_routine
    BEFORE UPDATE ON routines
    FOR EACH ROW EXECUTE FUNCTION guardar_historial_routine();

CREATE OR REPLACE FUNCTION verificar_usuario_physical_tracking() RETURNS TRIGGER AS $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM users WHERE id = NEW.user_id) THEN
        RAISE EXCEPTION 'Usuario no valido';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_verificar_usuario_physical_tracking
    BEFORE INSERT ON physical_tracking
    FOR EACH ROW EXECUTE FUNCTION verificar_usuario_physical_tracking();

-- ── Laravel tables ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255),
    created_at TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    payload TEXT NOT NULL,
    last_activity INTEGER NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_sessions_user ON sessions(user_id);
CREATE INDEX IF NOT EXISTS idx_sessions_activity ON sessions(last_activity);

CREATE TABLE IF NOT EXISTS cache (
    key VARCHAR(255) PRIMARY KEY,
    value TEXT NOT NULL,
    expiration INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS cache_locks (
    key VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS migrations (
    id SERIAL PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    batch INTEGER NOT NULL
);

COMMIT;
