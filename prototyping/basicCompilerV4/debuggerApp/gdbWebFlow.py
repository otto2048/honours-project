import gdb

# define handlers
def exit_handler (event):
    gdb.execute("quit")

def breakpoint_handler(event):
    print("breakpoint set")

def stop_handler(event):
    if (isinstance(event, gdb.BreakpointEvent)):
        print("FOR_SERVER\n")
        print("EVENT_ON_BREAK\n")
        print(event.breakpoint.location)
        print("EVENT_ON_BREAK_END\n")

def cont_handler(event):
    print("FOR_SERVER\n")
    print("EVENT_ON_CONTINUE\n")
    print("EVENT_ON_CONTINUE_END\n")

# attach handlers
gdb.events.stop.connect(stop_handler)

gdb.events.cont.connect(cont_handler)

gdb.events.breakpoint_created.connect(breakpoint_handler)

gdb.events.exited.connect(exit_handler)