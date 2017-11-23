#!/bin/bash

PROJECT_NAME="wordpress"
COMPOSE="orchestration.yml"
CONTAINERS=("${PROJECT_NAME}_server_1" "${PROJECT_NAME}_website_1" "${PROJECT_NAME}_database_1") 
VOLUMES=("${PROJECT_NAME}_database" "${PROJECT_NAME}_website")

# clean
for container in "${CONTAINERS[@]}"
do
    if [ "$(docker ps -aq -f name=$container)" ]; then
        docker rm -f $container
    fi
done

for volume in "${VOLUMES[@]}"
do
    if [ "$(docker volume ls -q -f name=$volume)" ]; then
        docker volume rm -f $volume
    fi
done

# build
docker-compose -p $PROJECT_NAME -f ./$COMPOSE build
docker-compose -p $PROJECT_NAME -f ./$COMPOSE up -d

exit 0
