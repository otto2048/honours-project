import gdb

class BreakpointDump(gdb.Command):

    def __init__(self):
        super(BreakpointDump, self).__init__(
            "breakpoint_dump", gdb.COMMAND_USER
        )

    def getBreakpoints():
        breakpoints = gdb.Breakpoint.locations
        return breakpoints
        

    def complete(self, text, word):
        return gdb.COMPLETE_SYMBOL
    
    def invoke(self, args, from_tty):
        print(gdb.Breakpoint.location)

BreakpointDump()