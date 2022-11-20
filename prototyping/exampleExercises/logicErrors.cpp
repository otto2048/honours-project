#include <iostream>

using std::cout;
using std::endl;

bool testDivision(int input)
{
    // if input is divisible by 2 and divisible by 10
    if (input % 2 || input % 10)
    {
        return true;
    }

    return false;
}

int main()
{
    int a = 10;
    int b = 5;
    int c = 0;

    c = a * b;

    cout << "A divided by B is: " << c << endl;

    if (testDivision(34))
    {
        cout << "34 is divisible by 2 and 10";
    }
    else
    {
        // CORRECT OUTPUT
        cout << "34 is not divisible by 2 and 10";
    }

}