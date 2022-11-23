#include <iostream>

using std::cout;
using std::endl;

//find the largest of 2 numbers
int largest(int a, int b)
{
    if (a > b)
    {
        return a;
    }
    
    return b;
}

//find the largest of 3 numbers
int largest(int a, int b, int c)
{
    // NEED TO CORRECT THIS LINE
    //if (a >= b && a >= c)
    if (a > b && a > c)
    {
        return a;
    }
    // NEED TO CORRECT THIS LINE
    //else if (b >= a && b >= c)
    else if (b > a && b > c)
    {
        return b;
    }
    else
    {
        return c;
    }
}

//find the largest of 6 numbers
//COULD USE DEBUGGER TO STEP INTO LARGEST FUNCTION TO FIND ERROR
int largest6(int a, int b, int c, int d, int e, int f)
{
    return largest(largest(a, b, c), largest(d, e, f));
}

int main()
{
    int u = 5;
    int v = 5;
    int w = 1;
    int x = 2;
    int y = 4;
    int z = 1;

    cout << largest6(u, v, w, x, y, z) << endl;
}