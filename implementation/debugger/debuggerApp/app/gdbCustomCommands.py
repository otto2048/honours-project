import gdb
import re
import pprint

#TODO: handle nested functions (multiple frames), also test on recursion

class StepOver(gdb.Command):

    def __init__(self):
        super(StepOver, self).__init__(
            "step_over", gdb.COMMAND_USER
        )

    def complete(self, text, word):
        return gdb.COMPLETE_SYMBOL
    
    def invoke(self, args, from_tty):
        gdb.execute("next", to_string = True)
        result = gdb.execute("where", to_string=True)

        print("FOR_SERVER\n")

        print("EVENT_ON_STEP\n")

        result_arr = result.split("\n")
        print(result_arr[0].split()[-1])

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

        result_arr = result.split(",")

        #silence the output from this breakpoint
        bp_num = result_arr[0].split()[1]
        gdb.execute("commands " + bp_num + " \nsilent \n end")

        file = result_arr[0].split()[-1]
        line = result_arr[1].split()[-1].split(".")[0]

        file_line = file + ":" + line

        if (line != args.split(":")[1]):
            print("FOR_SERVER")
            print("EVENT_ON_BP_CHANGED")
            #print old location
            print(args)
            #print the new location
            print(file_line)
            print("EVENT_ON_BP_CHANGED_END")

class ClearSilent(gdb.Command):
    def __init__(self):
        super(ClearSilent, self).__init__(
            "clear_silent", gdb.COMMAND_USER
        )

    def complete(self, text, word):
        return gdb.COMPLETE_SYMBOL
    
    def invoke(self, args, from_tty):
        result = gdb.execute("clear " + args, to_string = True)

class GetLocals(gdb.Command):
    def __init__(self):
        super(GetLocals, self).__init__(
            "get_locals", gdb.COMMAND_USER
        )

    def complete(self, text, word):
        return gdb.COMPLETE_SYMBOL
    
    def invoke(self, args, from_tty):
        frame = gdb.selected_frame()

        block = frame.block()

        names = set()

#https://stackoverflow.com/questions/30013252/get-all-global-variables-local-variables-in-gdbs-python-interface/31231722#31231722
        while block:
            if(block.is_global):
                print()
                print('global vars')
            for symbol in block:
                if (symbol.is_argument or symbol.is_variable):
                    name = symbol.name
                    if not name in names:
                        print('"{}" = "{}"'.format(name, symbol.value(frame)))

                        type = symbol.value(frame).type
                        typeCode = symbol.value(frame).type.code

                        if typeCode is gdb.TYPE_CODE_STRUCT or typeCode is gdb.TYPE_CODE_UNION or typeCode is gdb.TYPE_CODE_ENUM or typeCode is gdb.TYPE_CODE_FUNC:
                            print(type.fields())
                        
                        names.add(name)
            block = block.superblock

StepOver()
StepInto()
StepOut()
BreakSilent()
ClearSilent()
GetLocals()