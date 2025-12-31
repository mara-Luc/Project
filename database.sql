CREATE TABLE users (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100) NOT NULL,
    email           VARCHAR(255) UNIQUE,
    phone           VARCHAR(50),
    password_hash   VARCHAR(255) NOT NULL,
    role            ENUM('admin','operator','viewer') NOT NULL DEFAULT 'viewer',
    two_factor_enabled TINYINT(1) NOT NULL DEFAULT 0,
    two_factor_secret  VARCHAR(64),
    status          ENUM('active','disabled','banned') NOT NULL DEFAULT 'active',
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE user_photos (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    file_path   VARCHAR(255) NOT NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE rfid_cards (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    uid         VARCHAR(64) NOT NULL UNIQUE,
    user_id     INT NULL,
    card_type   ENUM('normal','magic_uid','cloneable') NOT NULL DEFAULT 'normal',
    sector_data JSON NULL,
    status      ENUM('active','lost','revoked') NOT NULL DEFAULT 'active',
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE pin_codes (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    pin_hash    VARCHAR(255) NOT NULL,
    status      ENUM('active','expired','revoked') NOT NULL DEFAULT 'active',
    attempts    INT NOT NULL DEFAULT 0,
    last_attempt DATETIME NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE camera_groups (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(255)
);

CREATE TABLE cameras (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    group_id        INT NULL,
    name            VARCHAR(100) NOT NULL,
    location        VARCHAR(255),
    rtsp_url        VARCHAR(255) NOT NULL,
    ip_address      VARCHAR(45),
    model           VARCHAR(100),
    status          ENUM('online','offline','degraded') NOT NULL DEFAULT 'offline',
    last_heartbeat  DATETIME NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES camera_groups(id) ON DELETE SET NULL
);

CREATE TABLE recordings (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    camera_id       INT NOT NULL,
    file_path       VARCHAR(255) NOT NULL,
    start_time      DATETIME NOT NULL,
    end_time        DATETIME NOT NULL,
    duration_sec    INT NOT NULL,
    frame_rate      DECIMAL(5,2),
    resolution      VARCHAR(50),
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (camera_id) REFERENCES cameras(id) ON DELETE CASCADE
);

CREATE TABLE access_rules (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT NOT NULL,
    camera_group_id INT NULL,
    allowed_from    TIME NOT NULL,
    allowed_to      TIME NOT NULL,
    days_of_week    JSON NOT NULL,
    valid_from      DATE NOT NULL,
    valid_to        DATE NULL,
    status          ENUM('active','disabled') NOT NULL DEFAULT 'active',
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (camera_group_id) REFERENCES camera_groups(id) ON DELETE SET NULL
);

CREATE TABLE events (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    timestamp       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    event_type      ENUM('rfid','pin','camera_motion','system','manual_override') NOT NULL,
    user_id         INT NULL,
    camera_id       INT NULL,
    credential_id   INT NULL,
    recording_id    INT NULL,
    success         TINYINT(1) NOT NULL DEFAULT 0,
    confidence      DECIMAL(5,2),
    metadata        JSON,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (camera_id) REFERENCES cameras(id) ON DELETE SET NULL,
    FOREIGN KEY (recording_id) REFERENCES recordings(id) ON DELETE SET NULL
);

CREATE TABLE device_status_logs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    device_type ENUM('camera','controller','sensor') NOT NULL,
    device_id   INT NULL,
    status      ENUM('online','offline','degraded','error') NOT NULL,
    message     VARCHAR(255),
    timestamp   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);