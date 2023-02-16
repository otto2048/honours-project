#include "exerciseThree.h"
#include &lt;iostream&gt;

int main()
{
    int num = 10;
    int factor = 5;
    int factorTwo = 3;

    if (testFactors(num, factor, factorTwo))
    {
        std::cout << "Both " << factor << " and " << factorTwo << " are factors of " << num;
    }
    else
    {
        std::cout << "Either or both " << factor << " and " << factorTwo << " are not factors of " << num;
    }
}