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


class StepInto(gdb.Command):
    def __init__(self):
        super(StepInto, self).__init__(
            "step_into", gdb.COMMAND_USER
        )

    def complete(self, text, word):
        return gdb.COMPLETE_SYMBOL
    
    def invoke(self, args, from_tty):
        gdb.execute("step", to_string = True)
        result = gdb.execute("where", to_string=True)

        result_arr = result.split("\n")

        print("FOR_SERVER\n")

        print("EVENT_ON_STEP\n")

        print(result_arr[0].split()[-1])

        print("EVENT_ON_STEP_END\n")

   
class StepOut(gdb.Command):
    def __init__(self):
        super(StepOut, self).__init__(
            "step_out", gdb.COMMAND_USER
        )

    def complete(self, text, word):
        return gdb.COMPLETE_SYMBOL
    
    def invoke(self, args, from_tty):
        gdb.execute("finish", to_string = True)
        result = gdb.execute("where", to_string=True)

        print(result)

        print("FOR_SERVER\n")

        print("EVENT_ON_STEP\n")

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

StepOver()
StepInto()
StepOut()
BreakSilent()
ClearSilent()