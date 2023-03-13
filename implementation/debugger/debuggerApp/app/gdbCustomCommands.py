import gdb
import json
import networkx as nx
from matplotlib import pyplot as plt
import random
import string

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

        print("DONE\n")


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
    
    def loadVariables(self, item, frame, graph, parent = "top_level"):

        typeCode = item[2].code

        # check if this item has fields
        if typeCode is gdb.TYPE_CODE_STRUCT or typeCode is gdb.TYPE_CODE_UNION or typeCode is gdb.TYPE_CODE_ENUM or typeCode is gdb.TYPE_CODE_FUNC:

            # connect this item to the graph
            child = (item[0], None, item[2].name, item[3])

            graph.add_edges_from([(parent, child)])

            fields = item[2].fields()

            # do this function for all the fields
            for field in fields:
                the_item = (field.name, item[1][field], item[1][field].type, generate_random_string(8))

                self.loadVariables(the_item, frame, graph, parent = child)
        
        # check if this item is an array
        elif typeCode is gdb.TYPE_CODE_ARRAY:
            firstItem = item[1][0]

            firstItemTC = firstItem.type.code

            # connect parent item to the graph
            child = (item[0], None, "array", item[3])
            graph.add_edges_from([(parent, child)])

            # get array size
            upper_limit = item[2].range()[1]
            x = range(upper_limit + 1)

            # check if this item has fields
            if firstItemTC is gdb.TYPE_CODE_STRUCT or firstItemTC is gdb.TYPE_CODE_UNION or firstItemTC is gdb.TYPE_CODE_ENUM or firstItemTC is gdb.TYPE_CODE_FUNC:
                the_parent = (item[0], None, "array", item[3])

                # do this function for all the elements
                for i in x:
                    the_item = (i, item[1][i], item[1][i].type, generate_random_string(8))

                    self.loadVariables(the_item, frame, graph, parent = the_parent)
            else:
                the_parent = (item[0], item[1].format_string(pretty_arrays = False), item[2].name, item[3])

                # add each element to the graph
                for i in x:    
                    child = (i, item[1][i].format_string(), item[1][i].type.name, generate_random_string(8))

                    graph.add_edges_from([(the_parent, child)])

        else:
            # add the variable to graph
            if typeCode is gdb.TYPE_CODE_ARRAY:
                child = (item[0], item[1].format_string(array_indexes = True, pretty_arrays = False), "array", generate_random_string(8))

                graph.add_edges_from([(parent, child)])
            else:
                child = (item[0], item[1].format_string(array_indexes = True, pretty_arrays = False), item[2].name, generate_random_string(8))

                graph.add_edges_from([(parent, child)])
            return
        
    def getVariables(self):
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
                        variables.append((name, value, type, generate_random_string(8)))
            block = block.superblock

        return variables
    
    def invoke(self, args, from_tty):
        frame = gdb.selected_frame()

        variables = self.getVariables()

        graph = nx.DiGraph()

        for item in variables:
            self.loadVariables(item, frame, graph)

        print("FOR_SERVER")
        print("EVENT_ON_LOCALS_DUMP")
        print(json.dumps({"data" : nx.node_link_data(graph)}))
        print("DONE")

        plt.rcParams["figure.figsize"] = (50,50)

        plt.tight_layout()
        nx.draw_networkx(graph, arrows=True)
        plt.savefig("g1.png", format="PNG")
        # tell matplotlib you're done with the plot: https://stackoverflow.com/questions/741877/how-do-i-tell-matplotlib-that-i-am-done-with-a-plot
        plt.clf()

class GetTopLevelLocals(gdb.Command):

    def __init__(self):
        super(GetTopLevelLocals, self).__init__(
            "get_top_level_locals", gdb.COMMAND_USER
        )

    def complete(self, text, word):
        return gdb.COMPLETE_SYMBOL
    
    def invoke(self, args, from_tty):
        frame = gdb.selected_frame()

        variables = getVariables()

        graph = nx.DiGraph()

        for item in variables:
            loadVariables(item, frame, graph, 0)

        print("FOR_SERVER")
        print("EVENT_ON_LOCALS_DUMP")
        print(json.dumps({"data" : nx.node_link_data(graph)}))
        print("DONE")

class GetTopLevelLocalsNames(gdb.Command):
    def __init__(self):
        super(GetTopLevelLocalsNames, self).__init__(
            "get_top_level_locals_names", gdb.COMMAND_USER
        )

    def complete(self, text, word):
        return gdb.COMPLETE_SYMBOL
    
    def invoke(self, args, from_tty):
        variables = getVariables()

        ret = []

        for i in variables:
            ret.append(i[3])

        print("FOR_SERVER")
        print("EVENT_ON_TOP_LEVEL_VARIABLES")
        print(json.dumps({"data" : ret}))
        print("DONE")

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

        loadVariables((arguments[0], var, var.type, arguments[2]), frame, graph, int(arguments[1]))

        print("FOR_SERVER")
        print("EVENT_ON_DUMP_LOCAL")
        print(json.dumps({"data" : nx.node_link_data(graph)}))
        print("DONE")

def generate_random_string(len):
    return ''.join(random.choice(string.ascii_letters + string.digits) for i in range(len))

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
                    variables.append((name, value, type, name))
        block = block.superblock

    return variables

def loadVariables(item, frame, graph, recurse_limit, parent = "top_level", level = 0):

    typeCode = item[2].code

    # check if this item has fields
    if typeCode is gdb.TYPE_CODE_STRUCT or typeCode is gdb.TYPE_CODE_UNION or typeCode is gdb.TYPE_CODE_ENUM or typeCode is gdb.TYPE_CODE_FUNC:

        fields = item[2].fields()

        # connect this item to the graph
        if len(fields) > 0:
            child = (item[0], None, item[2].name, item[3])
        else:
            child = (item[0], "Object", item[2].name, item[3])

        graph.add_edges_from([(parent, child)])

        new_level = level + 1

        if new_level <= recurse_limit:
            # do this function for all the fields
            for field in fields:
                item_id = item[3] + "_" + field.name
                the_item = (field.name, item[1][field], item[1][field].type, item_id)

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
            child = (item[0], None, "array", item[3])
            graph.add_edges_from([(parent, child)])

            new_level = level + 1

            if new_level <= recurse_limit:

                # check if this item has fields
                if firstItemTC is gdb.TYPE_CODE_STRUCT or firstItemTC is gdb.TYPE_CODE_UNION or firstItemTC is gdb.TYPE_CODE_ENUM or firstItemTC is gdb.TYPE_CODE_FUNC or firstItemTC is gdb.TYPE_CODE_ARRAY:
                    the_parent = (item[0], None, "array", item[3])

                    # do this function for all the elements
                    for i in x:
                        item_id = item[3] + "_" + str(i)
                        the_item = (i, item[1][i], item[1][i].type, item_id)

                        loadVariables(the_item, frame, graph, recurse_limit, parent = the_parent, level = new_level)
                else:
                    the_parent = (item[0], item[1].format_string(pretty_arrays = False), item[2].name, item[3])

                    # add each element to the graph
                    for i in x:
                        item_id = item[3] + "_" + str(i)
                        child = (i, item[1][i].format_string(), item[1][i].type.name, item_id)

                        graph.add_edges_from([(the_parent, child)])
        else:
            # connect parent item to the graph
            child = (item[0], "Empty", "array", item[3])
            graph.add_edges_from([(parent, child)])

    else:
        # add the variable to graph
        item_id = item[3] + "_" + item[0]
        if typeCode is gdb.TYPE_CODE_ARRAY:
            child = (item[0], item[1].format_string(array_indexes = True, pretty_arrays = False), "array", item_id)

            graph.add_edges_from([(parent, child)])
        else:
            child = (item[0], item[1].format_string(array_indexes = True, pretty_arrays = False), item[2].name, item_id)

            graph.add_edges_from([(parent, child)])
        return

StepOver()
StepInto()
StepOut()
BreakSilent()
ClearSilent()
GetLocals()
GetTopLevelLocals()
GetTopLevelLocalsNames()
GetLocal()