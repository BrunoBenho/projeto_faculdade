CREATE DATABASE IF NOT EXISTS processostrabalhistas CHARACTER SET utf8mb4;
USE processostrabalhistas;

CREATE TABLE IF NOT EXISTS processos (
    registro INT PRIMARY KEY AUTO_INCREMENT,
    advogado VARCHAR(100),
    trabalhador VARCHAR(100),
    empresa VARCHAR(100)
);

-- Inclusão (pelo menos dois processos)
INSERT INTO processos (advogado, trabalhador, empresa) VALUES
('Bruno Benho', 'Murilo Arantes', 'Concilig'),
('Geovana', 'Gabriel', 'Hcosta');

-- Consulta
SELECT * FROM processos;
