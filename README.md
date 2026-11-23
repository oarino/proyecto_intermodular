# Demo CI/CD en contenedores — Proyecto Intermodular (PHP + MySQL)

Ejemplo mínimo pero real de integración continua y despliegue continuo,
pensado para acompañar el desarrollo del proyecto intermodular mientras
se termina el modelo de datos. No sustituye a un servidor de producción
serio, pero reproduce el mecanismo real con el menor número de piezas
posible.

## Qué hace

1. **Integración continua (CI):** cada vez que se hace `push` (o se abre
   un pull request), GitHub Actions comprueba que el código PHP no tiene
   errores de sintaxis, construye la imagen Docker y levanta todo el
   stack para comprobar que responde. Si algo falla, el pipeline se para
   ahí y avisa — nada se despliega.
2. **Despliegue continuo (CD):** si el `push` es a `main` y la CI ha ido
   bien, un segundo job reconstruye la imagen con el código más reciente
   y relanza los contenedores. La versión desplegada queda marcada con
   el hash del commit, visible en la propia página.

## Estructura

```
docker-compose.yml          # los tres servicios: app, db, phpmyadmin
php/Dockerfile               # imagen de la aplicación PHP
src/                          # el código de la aplicación
db/init.sql                   # esquema y datos de prueba, se carga solo al crear el contenedor de MySQL
.github/workflows/ci-cd.yml   # el pipeline
scripts/deploy.sh             # el mismo despliegue, pero a mano
```

## Probarlo en local (sin nada de CI/CD todavía)

```bash
cp .env.example .env
docker compose up -d --build
```

- Aplicación: http://localhost:8080
- phpMyAdmin: http://localhost:8081 (usuario/contraseña del `.env`)

Cada cambio en `src/` requiere `docker compose up -d --build` para verse
reflejado, porque la imagen copia el código dentro en el momento de
construirse (así el pipeline construye siempre un artefacto autocontenido,
no depende de que el código exista fuera del contenedor).

## Cómo probar el pipeline completo

El job de **integración continua** funciona nada más subir el repositorio
a GitHub: no requiere configuración adicional, corre en los runners
gratuitos de GitHub. Con eso ya tenéis CI real.

Para el job de **despliegue continuo** hace falta un "servidor" donde
desplegar. La opción más humilde, sin gastar nada ni depender de ningún
proveedor externo, es usar como servidor el mismo ordenador donde
desarrolláis (o un portátil/mini-PC del aula que se quede encendido):

1. En GitHub: **Settings → Actions → Runners → New self-hosted runner**.
2. Elegid el sistema operativo y seguid los comandos que GitHub genera
   (descargan un paquete, lo configuran con un token del repositorio).
3. Al final, ese runner queda registrado escuchando: cuando lancéis
   `./run.sh` (o lo instaléis como servicio con `./svc.sh install && ./svc.sh start`),
   ese ordenador pasa a ejecutar el job `deploy` cada vez que alguien
   haga push a `main`.
4. Necesita tener Docker y Docker Compose instalados — es el mismo
   ordenador el que reconstruye y relanza los contenedores.

Con eso, el flujo completo en clase queda así: alguien hace un cambio,
lo sube a `main`, en la pestaña *Actions* de GitHub se ve primero el job
de CI en verde y después el de despliegue, y al recargar
`http://localhost:8080` (o la IP del ordenador-servidor) aparece la
nueva "versión desplegada" con el hash del commit recién subido.

Si en algún momento el runner no está levantado, `scripts/deploy.sh`
hace exactamente lo mismo a mano.

## Si más adelante tenéis un servidor de verdad (VPS, Raspberry Pi…)

El self-hosted runner deja de ser cómodo si el servidor no es siempre el
mismo ordenador que tenéis delante. La alternativa igual de humilde para
ese caso:

1. El job de CI construye la imagen y la sube a un registro (por ejemplo
   `ghcr.io`, integrado en GitHub y gratuito para repos del centro).
2. En el servidor, un contenedor llamado **Watchtower**
   (`containrrr/watchtower`) vigila ese registro cada cierto tiempo y,
   si encuentra una imagen más reciente, él solo hace `docker compose
   pull` + `up -d`.

Esta opción evita manejar claves SSH o secretos de despliegue: el
servidor tira de la imagen en vez de que GitHub empuje hacia él. Es un
paso natural cuando dejéis de usar vuestro propio ordenador como
servidor, pero no hace falta montarlo ahora.

## Notas de seguridad para el aula

- Mantened el repositorio del proyecto **privado**: un runner
  self-hosted ejecuta el código de cualquier pull request que le
  llegue, y en un repositorio público cualquiera podría abrir uno.
- El pipeline solo despliega en `push` a `main`, nunca en `pull_request`
  — así un cambio propuesto (aún no revisado) no puede tocar el
  servidor.
- Las contraseñas de `.env` en este demo son solo de ejemplo: cambiadlas
  si el proyecto llega a manejar datos reales de algún tipo.
