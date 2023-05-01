import gdb

# pretty printer to handle strings
# source: (GDB, no date)
class StringPrettyPrinter(object):
    def __init__(self, val):
        self.val = val

    def to_string(self):
        return self.val['_M_dataplus']['_M_p']
    
def build_pretty_printer():
    pp = gdb.printing.RegexpCollectionPrettyPrinter(
        "my_library")
    pp.add_printer('std::__cxx11::basic_string<char,.*>', '^std::__cxx11::basic_string<char,.*>$', StringPrettyPrinter)
    return pp

gdb.printing.register_pretty_printer(
    gdb.current_objfile(),
    build_pretty_printer())