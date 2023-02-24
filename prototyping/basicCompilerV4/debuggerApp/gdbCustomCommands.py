import gdb

class StepForward(gdb.Command):

    def __init__(self):
        super(StepForward, self).__init__(
            "step_forward", gdb.COMMAND_USER
        )

    def complete(self, text, word):
        return gdb.COMPLETE_SYMBOL
    
    def invoke(self, args, from_tty):
        print("EVENT_ON_STEP")
        result = gdb.execute("s", to_string = True)
        print(result)
        print("EVENT_ON_STEP_END")

StepForward()