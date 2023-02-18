#include "exerciseThree.h"

bool testFactors(int a, int factor, int factorTwo)
{
    //if (a % factor || a % factorTwo)
    if (a % factor && a % factorTwo)
    {
        //return false;
        return true;
    }

    //return true;
    return false;
}