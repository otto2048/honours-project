FROM node:12

WORKDIR /compilerApp

COPY . .

RUN npm install ws
RUN npm install async

EXPOSE 8080

CMD ["node", "server.js"]