FROM otto2048/environment_base:latest

RUN apt-get install python3-pip -y
RUN pip3 install networkx
RUN pip3 install matplotlib

# set up gdbinit
WORKDIR /root/.config/gdb
COPY gdbinit .

# add app files
WORKDIR /debuggerApp

COPY /app .

RUN npm install

EXPOSE 8080

CMD ["node", "server.js"]