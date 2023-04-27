import gdb

# define GDB event handlers
def exit_handler (event):
    
    print("FOR_SERVER")
    print("EVENT_ON_PROG_EXIT")
    exit_code_str = gdb.execute("print $_exitcode", to_string=True)
    exit_code = exit_code_str.split()
    print("Program exited with code: " + exit_code[-1])
    print("DONE")

    gdb.execute("quit")

def stop_handler(event):
    if (isinstance(event, gdb.BreakpointEvent)):
        print("FOR_SERVER\n")
        print("EVENT_ON_BREAK\n")
        location = event.breakpoint.locations[0].source
        print(location[0] + ":" + str(location[1]))
        print("DONE\n")

def cont_handler(event):
    print("FOR_SERVER\n")
    print("EVENT_ON_CONTINUE\n")
    print("DONE\n")

# attach handlers
gdb.events.stop.connect(stop_handler)

gdb.events.cont.connect(cont_handler)

gdb.events.exited.connect(exit_handler)