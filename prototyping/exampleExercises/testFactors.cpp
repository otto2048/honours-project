#include <iostream>

using std::cout;
using std::endl;

bool testFactors(int a, int factor, int factorTwo)
{
    // NEED TO CORRECT THIS LINE
    //if (a % factor || a % factorTwo)
    if (a % factor && a % factorTwo)
    {
        // NEED TO CORRECT THIS LINE
        //return false;
        return true;
    }

    // NEED TO CORRECT THIS LINE
    //return true;
    return false;
}

int main()
{
    int a = 10;
    int b = 5;
    int c = 2;

    if (testFactors(a, b, c))
    {
        cout << b << " and " << c << " are both factors of " << a << endl;
    }
    else
    {   
        cout << "one or both of the factors (" << b << ", " << c << ") are not factors of " << a << endl;
    }
}