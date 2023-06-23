FROM node:18-alpine as frontend-stage

LABEL maintainer="DuyDev"

WORKDIR /var/www/html/frontend

COPY . .

EXPOSE 3000

CMD [ "yarn", "dev" ]
