FROM otto2048/environment_base:latest

# set up gdbinit
WORKDIR /root/.config/gdb
COPY gdbinit .

# add app files
WORKDIR /debuggerApp

COPY /app .

RUN npm install

EXPOSE 8080

CMD ["node", "server.js"]