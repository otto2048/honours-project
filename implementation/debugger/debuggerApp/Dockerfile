FROM node:12

# update
RUN apt-get update -y
RUN apt-get update

# install dependencies for googletest
RUN apt-get install cmake -y
RUN apt-get install gdb -y

# install dependencies for gdb v13
RUN apt-get install python3-dev -y
RUN apt-get install libgmp-dev -y

# build gdb v13
RUN wget "http://ftp.gnu.org/gnu/gdb/gdb-13.1.tar.gz"
RUN tar -xvzf gdb-13.1.tar.gz
WORKDIR /gdb-13.1
RUN ./configure --with-python=/usr/bin/python3
RUN make
RUN make install

# set up gdbinit
WORKDIR /home/node
COPY .gdbinit .

# build googletest framework
RUN git clone https://github.com/google/googletest.git -b v1.13.0
WORKDIR /googletest
RUN mkdir build
WORKDIR /build
RUN cmake /googletest -DBUILD_GMOCK=OFF
RUN make
RUN make install

WORKDIR /debuggerApp

COPY /app .

RUN npm install

EXPOSE 8080

CMD ["node", "server.js"]