CREATE TABLE IF NOT EXISTS alumnos_demo (
  id     INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  grupo  VARCHAR(20)  NOT NULL
);

INSERT INTO alumnos_demo (nombre, grupo) VALUES
  ('Alumno Demo 1', '2 DAW'),
  ('Alumno Demo 2', '2 DAW'),
  ('Alumno Demo 3', '2 DAM');
