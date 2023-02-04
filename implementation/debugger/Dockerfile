FROM node:12

WORKDIR /compilerApp

COPY . .

RUN npm install ws

EXPOSE 8080

CMD ["node", "server.js"]