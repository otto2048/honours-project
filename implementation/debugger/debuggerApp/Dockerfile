FROM node:12

RUN apt-get update -y
RUN apt-get update
RUN apt-get install cmake -y

RUN git clone https://github.com/google/googletest.git -b v1.13.0
WORKDIR /googletest
RUN mkdir build
WORKDIR /build
RUN cmake /googletest -DBUILD_GMOCK=OFF
RUN make
RUN make install

WORKDIR /debuggerApp

COPY . .

RUN npm install

EXPOSE 8080

CMD ["node", "server.js"]