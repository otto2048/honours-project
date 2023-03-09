import gdb
import json

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
    
    def loadVariables(self, item, frame, dict, parent = "top_level"):

        typeCode = item[2].code

        # check if this item has fields
        if typeCode is gdb.TYPE_CODE_STRUCT or typeCode is gdb.TYPE_CODE_UNION or typeCode is gdb.TYPE_CODE_ENUM or typeCode is gdb.TYPE_CODE_FUNC:

            # start a new nested dictionary
            dict[item[0]] = {}

            fields = item[2].fields()

            # do this function for all the fields
            for field in fields:
                self.loadVariables((field.name, item[1][field], (item[1][field].type)), frame, dict, parent = item[0])
        
        # check if this item is an array
        if typeCode is gdb.TYPE_CODE_ARRAY:
            firstItem = item[1][0]

            firstItemTC = firstItem.type.code

            # check if this item has fields
            if firstItemTC is gdb.TYPE_CODE_STRUCT or firstItemTC is gdb.TYPE_CODE_UNION or firstItemTC is gdb.TYPE_CODE_ENUM or firstItemTC is gdb.TYPE_CODE_FUNC:

                # start a new nested dictionary
                dict[item[0]] = {}

                upper_limit = item[2].range()[1]

                x = range(upper_limit)

                # do this function for all the elements
                for i in x:
                    self.loadVariables((i, item[1][i], (item[1][i].type)), frame, dict, parent = item[0])

        else:
            # add the variable to dictionary
            if typeCode is gdb.TYPE_CODE_ARRAY:
                dict[parent][item[0]] = (item[1].format_string(array_indexes = True), "array")
            else:
                dict[parent][item[0]] = (item[1].format_string(array_indexes = True), item[2].name)
            return
        
    def getVariables(self):
        frame = gdb.selected_frame()

        block = frame.block()

        names = set()
        variables = []

        #https://stackoverflow.com/questions/30013252/get-all-global-variables-local-variables-in-gdbs-python-interface/31231722#31231722
        while block:
            if(block.is_global):
                print()
                print('global vars')
            for symbol in block:
                if (symbol.is_argument or symbol.is_variable):
                    name = symbol.name
                    if not name in names:
                        value = symbol.value(frame)
                        type = symbol.value(frame).type
                        
                        names.add(name)
                        variables.append((name, value, type))
            block = block.superblock

        return variables
    
    def invoke(self, args, from_tty):
        frame = gdb.selected_frame()

        variables = self.getVariables()

        dictionary = {}
        dictionary["top_level"] = {}

        for item in variables:
            self.loadVariables(item, frame, dictionary)

        print(dictionary)
        #print(json.dumps(dictionary))

StepOver()
StepInto()
StepOut()
BreakSilent()
ClearSilent()
GetLocals()