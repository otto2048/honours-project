import gdb

def exit_handler (event):
    gdb.execute("quit")

def breakpoint_handler(event):
    print("breakpoint set")

def stop_handler(event):
    if (isinstance(event, gdb.BreakpointEvent)):
        print(event.breakpoint.location)
        print("PROGRAM_OUTPUT this is a breakpoint event")
    print("PROGRAM_OUTPUT program has stopped")

gdb.events.stop.connect(stop_handler)

gdb.events.breakpoint_created.connect(breakpoint_handler)

gdb.events.exited.connect(exit_handler)