// EXERCISE ONE
// SHOW THE USER VALUE OF A AT THE END OF THE PROGRAM (STEP OVER AT BREAKPOINT)
// VALUE YOU NEED WILL DISPLAY AFTER THE LINE OF CODE HAS EXECUTED
// READ ONLY CODE

int main()
{
    int a = 0;

    //breakpoint to show that execution can be paused a variable values can be found at runtime
    a = a + 10;

    //...continue processing

    return 0;
}

// EXERCISE TWO
// ASK THE USER TO DETERMINE THE VALUE OF A AT THE END OF THE PROGRAM (STEP OVER AT BREAKPOINT)
// READ ONLY CODE

#include <cstdlib>
#include <ctime>

int main()
{
    //initialise random number generator with time
    srand(time(0));

    int a = 0;

    //breakpoint needed here
    a = rand() % 100;
    
    //...continue processing

    //or breakpoint needed here
    return 0;
}
