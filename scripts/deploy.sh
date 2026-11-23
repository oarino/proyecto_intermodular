#!/usr/bin/env bash
# Despliegue manual: hace exactamente lo mismo que el job "deploy" del
# pipeline, pero a mano. Útil para probar el flujo antes de configurar
# el runner, o como plan B si un día el runner no está levantado.
set -euo pipefail

GIT_SHA=$(git rev-parse --short HEAD)
export GIT_SHA

echo "Desplegando versión ${GIT_SHA}..."
docker compose down
docker compose build --build-arg GIT_SHA="${GIT_SHA}"
docker compose up -d

echo "Despliegue completado -> http://localhost:8080"
