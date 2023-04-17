// api Response class

class Response {
    constructor(sender_) {
        this.sender = sender_;
        this.event = null;
        this.value = null;
    }
}

module.exports = Response;