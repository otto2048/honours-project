import gdb

changedBreakpoints = []

class StepOver(gdb.Command):

    def __init__(self):
        super(StepOver, self).__init__(
            "step_over", gdb.COMMAND_USER
        )

    def complete(self, text, word):
        return gdb.COMPLETE_SYMBOL
    
    def invoke(self, args, from_tty):
        gdb.execute("next", to_string = True)

        print("FOR_SERVER\n")

        print("EVENT_ON_STEP\n")

        result = gdb.execute("where", to_string=True)
        result_arr = result.split()
        print(result_arr[-1])

        print("EVENT_ON_STEP_END\n")


class BreakSilent(gdb.Command):
    def __init__(self):
        super(BreakSilent, self).__init__(
            "break_silent", gdb.COMMAND_USER
        )

    def complete(self, text, word):
        return gdb.COMPLETE_SYMBOL
    
    def invoke(self, args, from_tty):
        result = gdb.execute("break " + args, to_string=True)
        result_arr = result.split()
        result_arr.pop(-1)

        if (result_arr[-1].split(".")[0] != args.split(":")[1]):
            print("FOR_SERVER")
            print("EVENT_ON_BP_CHANGED")
            #print old location
            print(args)
            #print the new location
            print(result_arr[5].split(",")[0] + ":" + result_arr[-1].split(".")[0])
            print("EVENT_ON_BP_CHANGED_END")
        #     changedBreakpoints.append(result_arr[-1])

class ClearSilent(gdb.Command):
    def __init__(self):
        super(ClearSilent, self).__init__(
            "clear_silent", gdb.COMMAND_USER
        )

    def complete(self, text, word):
        return gdb.COMPLETE_SYMBOL
    
    def invoke(self, args, from_tty):
        result = gdb.execute("clear " + args, to_string = True)

# a command to tell the server that all initial breakpoints have been set
class ReadyToRun(gdb.Command):
    def __init__(self):
        super(ReadyToRun, self).__init__(
            "run_ready", gdb.COMMAND_USER
        )

    def complete(self, text, word):
        return gdb.COMPLETE_SYMBOL
    
    def invoke(self, args, from_tty):
        print("FOR_SERVER")
        print("EVENT_ON_READY_TO_RUN")

        #get locations of breakpoints set
        result = gdb.execute("info break", to_string=True)
        result_arr = result.split("\n")
        result_arr.pop(0)
        result_arr.pop(-1)

        #output breakpoint locations
        for x in result_arr:
            x = x.split()
            print(x[-1])

        print("EVENT_ON_READY_TO_RUN_END")

StepOver()
BreakSilent()
ClearSilent()
ReadyToRun()