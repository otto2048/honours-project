import gdb

class StepForward(gdb.Command):

    def __init__(self):
        super(StepForward, self).__init__(
            "step_forward", gdb.COMMAND_USER
        )

    def complete(self, text, word):
        return gdb.COMPLETE_SYMBOL
    
    def invoke(self, args, from_tty):
        gdb.execute("s", to_string = True)

        print("EVENT_ON_STEP")

        result = gdb.execute("where", to_string=True)
        result_arr = result.split()
        print(result_arr[-1])
        
        print("EVENT_ON_STEP_END")


class BreakSilent(gdb.Command):
    def __init__(self):
        super(BreakSilent, self).__init__(
            "break_silent", gdb.COMMAND_USER
        )

    def complete(self, text, word):
        return gdb.COMPLETE_SYMBOL
    
    def invoke(self, args, from_tty):
        result = gdb.execute("break " + args, to_string = True)


class ClearSilent(gdb.Command):
    def __init__(self):
        super(ClearSilent, self).__init__(
            "clear_silent", gdb.COMMAND_USER
        )

    def complete(self, text, word):
        return gdb.COMPLETE_SYMBOL
    
    def invoke(self, args, from_tty):
        result = gdb.execute("clear " + args, to_string = True)

StepForward()
BreakSilent()
ClearSilent()