
const OP_INPUT = "INPUT";
const OP_COMPILE = "COMPILE";
const OP_TEST = "TEST";

const EVENT_ON_BREAK = "EVENT_ON_BREAK";
const EVENT_ON_CONTINUE = "EVENT_ON_CONTINUE";
const EVENT_ON_STEP = "EVENT_ON_STEP";
const EVENT_ON_STDOUT = 1;
const EVENT_ON_COMPILE_SUCCESS = 2;
const EVENT_ON_COMPILE_FAILURE = 3;
const EVENT_ON_PROGRAM_EXIT = 4;
const EVENT_ON_BREAKPOINT_CHANGED = "EVENT_ON_BP_CHANGED";

const SENDER_HOST = "HOST_SERVER";
const SENDER_USER = "USER_SENDER";
const SENDER_DEBUGGER = "DEBUGGER_SENDER";

export {OP_COMPILE, OP_INPUT, OP_TEST,
        EVENT_ON_BREAK, EVENT_ON_COMPILE_FAILURE, EVENT_ON_COMPILE_SUCCESS, EVENT_ON_STEP,
        EVENT_ON_STDOUT, EVENT_ON_PROGRAM_EXIT, EVENT_ON_CONTINUE, 
        EVENT_ON_BREAKPOINT_CHANGED,
        SENDER_DEBUGGER, SENDER_HOST, SENDER_USER}