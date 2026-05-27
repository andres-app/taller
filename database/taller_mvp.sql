
CREATE TABLE empresas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255),
    email VARCHAR(255)
);

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT,
    usuario VARCHAR(100),
    password VARCHAR(255),
    rol VARCHAR(50),
    FOREIGN KEY (empresa_id) REFERENCES empresas(id)
);

CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT,
    nombre VARCHAR(255),
    telefono VARCHAR(50),
    direccion TEXT
);

CREATE TABLE vehiculos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT,
    cliente_id INT,
    placa VARCHAR(20),
    marca VARCHAR(100),
    modelo VARCHAR(100),
    kilometraje VARCHAR(50)
);

CREATE TABLE ordenes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT,
    vehiculo_id INT,
    descripcion TEXT,
    total DECIMAL(10,2),
    estado VARCHAR(50)
);

INSERT INTO empresas(nombre,email)
VALUES ('Taller Demo','demo@taller.com');

INSERT INTO usuarios(empresa_id,usuario,password,rol)
VALUES (
    1,
    'admin',
    '$2y$10$W4x1D6R7Y2O1S9I4kF2L7OvZ9J3vB6lQjQhE3z9y6cK6xYj7k4e1m',
    'admin'
);
