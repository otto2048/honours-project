import gdb
import json
import networkx as nx

#TODO: handle nested functions (multiple frames), also test on recursion

# custom next command
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

        print("DONE\n")

# custom step command
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

        print("DONE\n")

# custom finish command
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

        print("DONE\n")

# custom break command
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
            print("DONE\n")

# custom clear command
class ClearSilent(gdb.Command):
    def __init__(self):
        super(ClearSilent, self).__init__(
            "clear_silent", gdb.COMMAND_USER
        )

    def complete(self, text, word):
        return gdb.COMPLETE_SYMBOL
    
    def invoke(self, args, from_tty):
        result = gdb.execute("clear " + args, to_string = True)

# custom GDB command that gets the locals in a frame
class GetTopLevelLocals(gdb.Command):

    def __init__(self):
        super(GetTopLevelLocals, self).__init__(
            "get_top_level_locals", gdb.COMMAND_USER
        )

    def complete(self, text, word):
        return gdb.COMPLETE_SYMBOL
    
    def invoke(self, args, from_tty):
        frame = gdb.selected_frame()

        # get variables
        variables = getVariables()

        graph = nx.DiGraph()

        # load all variables into the graph
        for item in variables:
            loadVariables(item, frame, graph, 0)

        # print graph json
        print("FOR_SERVER")
        print("EVENT_ON_LOCALS_DUMP")
        print(json.dumps({"data" : nx.node_link_data(graph)}))
        print("DONE")

# custom GDB command that gets a local variable
# args: variable name, recursion limit
class GetLocal(gdb.Command):

    def __init__(self):
        super(GetLocal, self).__init__(
            "get_local", gdb.COMMAND_USER
        )

    def complete(self, text, word):
        return gdb.COMPLETE_SYMBOL
    
    def invoke(self, args, from_tty):

        arguments = args.split()

        var = gdb.parse_and_eval(arguments[0])

        frame = gdb.selected_frame()
        graph = nx.DiGraph()

        # load variable details into the graph
        loadVariables((arguments[0], var, var.type, arguments[0], arguments[0]), frame, graph, int(arguments[1]))

        # print json version of the graph
        print("FOR_SERVER")
        print("EVENT_ON_DUMP_LOCAL")
        print(json.dumps({"data" : nx.node_link_data(graph)}))
        print("DONE")

# function that gets all the variables in a frame
def getVariables():
    frame = gdb.selected_frame()

    block = frame.block()

    names = set()
    variables = []

    #https://stackoverflow.com/questions/30013252/get-all-global-variables-local-variables-in-gdbs-python-interface/31231722#31231722
    while block:
        for symbol in block:
            if (symbol.is_argument or symbol.is_variable):
                name = symbol.name
                if not name in names:
                    value = symbol.value(frame)
                    type = symbol.value(frame).type
                    
                    names.add(name)

                    variables.append((name, value, type, name, name))
        block = block.superblock

    return variables

# function that loads a local variable into a standard format, preserving links to parent variables
# item and parent are variables in a standard format with the structure:
#   name, value, type, id, display name
# recurse_limit tells us far we should load variables within variables, level keeps track of this
def loadVariables(item, frame, graph, recurse_limit, parent = "top_level", level = 0):

    # get the variable type
    typeCode = item[2].code

    # check if this is a pointer or reference
    if typeCode is gdb.TYPE_CODE_PTR or typeCode is gdb.TYPE_CODE_REF:
        dereferenced = item[1].referenced_value()

        child_type = ""
        
        if typeCode is gdb.TYPE_CODE_PTR:
            child_type = dereferenced.type.name + " *"
        else:
            child_type = dereferenced.type.name + " &"

        child = (item[0], None, child_type, item[3], item[4])

        graph.add_edges_from([(parent, child)])

        new_level = level + 1

        if new_level <= recurse_limit:
            item_id = item[3] + "_value"
            the_item = (item[3], dereferenced, dereferenced.type, item_id, "[value]")

            loadVariables(the_item, frame, graph, recurse_limit, parent = child, level = new_level)

    # check if this item has fields (has variables within this one)
    elif typeCode is gdb.TYPE_CODE_STRUCT or typeCode is gdb.TYPE_CODE_UNION or typeCode is gdb.TYPE_CODE_ENUM or typeCode is gdb.TYPE_CODE_FUNC:

        fields = item[2].fields()

        # connect this item to the graph
        if len(fields) > 0:
            child = (item[0], None, item[2].name, item[3], item[4])
        else:
            child = (item[0], "Object", item[2].name, item[3], item[4])

        graph.add_edges_from([(parent, child)])

        new_level = level + 1

        if new_level <= recurse_limit:
            # do this function for all the fields
            for field in fields:
                item_id = item[3] + "_" + field.name
                the_item = (field.name, item[1][field], item[1][field].type, item_id, field.name)

                loadVariables(the_item, frame, graph, recurse_limit, parent = child, level = new_level)

    # check if this item is an array
    elif typeCode is gdb.TYPE_CODE_ARRAY:
        # get array size
        upper_limit = item[2].range()[1]
        x = range(upper_limit + 1)

        if upper_limit > 0:
            firstItem = item[1][0]

            firstItemTC = firstItem.type.code

            # connect parent item to the graph
            child = (item[0], None, "array", item[3], item[4])
            graph.add_edges_from([(parent, child)])

            new_level = level + 1

            if new_level <= recurse_limit:

                # check if this item has fields
                if firstItemTC is gdb.TYPE_CODE_STRUCT or firstItemTC is gdb.TYPE_CODE_UNION or firstItemTC is gdb.TYPE_CODE_ENUM or firstItemTC is gdb.TYPE_CODE_FUNC or firstItemTC is gdb.TYPE_CODE_ARRAY:    
                    the_parent = (item[0], None, "array", item[3], item[4])

                    # do this function for all the elements
                    for i in x:
                        item_id = item[3] + "_" + str(i)
                        display_name = "[" + str(i) + "]"

                        the_item = (i, item[1][i], item[1][i].type, item_id, display_name)

                        loadVariables(the_item, frame, graph, recurse_limit, parent = the_parent, level = new_level)
                else:
                    the_parent = (item[0], item[1].format_string(pretty_arrays = False), item[2].name, item[3])

                    # add each element to the graph
                    for i in x:
                        item_id = item[3] + "_" + str(i)
                        display_name = "[" + str(i) + "]"
                        child = (i, item[1][i].format_string(), item[1][i].type.name, item_id, display_name)

                        graph.add_edges_from([(the_parent, child)])
        else:
            # connect parent item to the graph
            child = (item[0], "Empty", "array", item[3], item[4])
            graph.add_edges_from([(parent, child)])

    else:
        # add the variable to graph
        item_id = item[3] + "_" + item[0]
        if typeCode is gdb.TYPE_CODE_ARRAY:
            child = (item[0], item[1].format_string(array_indexes = True, pretty_arrays = False), "array", item_id, item[4])

            graph.add_edges_from([(parent, child)])
        else:
            child = (item[0], item[1].format_string(array_indexes = True, pretty_arrays = False), item[2].name, item_id, item[4])

            graph.add_edges_from([(parent, child)])
        return

StepOver()
StepInto()
StepOut()
BreakSilent()
ClearSilent()
GetTopLevelLocals()
GetLocal()