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

// incorrect 
// int largest(int a, int b, int c)
// {
//     if (a > b && a > c)
//     {
//         return a;
//     }
//     else if (b > a && b > c)
//     {
//         return b;
//     }
//     else
//     {
//         return c;
//     }
// }

int largest(int a, int b, int c)
{
    if (a >= b && a >= c)
    {
        return a;
    }
    else if (b >= a && b >= c)
    {
        return b;
    }
    else
    {
        return c;
    }
}

int largest(int a, int b)
{
    if (a > b)
    {
        return a;
    }
    
    return b;
}

int largest4(int a, int b, int c, int d)
{
    return largest(largest(a, b), largest(c, d));
}

int largest6(int a, int b, int c, int d, int e, int f)
{
    return largest(largest(a, b, c), largest(d, e, f));
}

int largestArrElement(int arr[], int size)
{
    int largest = arr[0];

    for (int i=0; i<size; i++)
    {
        if (arr[i] > largest)
        {
            largest = arr[i];
        }
    }

    return largest;
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
        cout << "34 is divisible by 2 and 10" << endl;
    }
    else
    {
        // CORRECT OUTPUT
        cout << "34 is not divisible by 2 and 10" << endl;
    }

    int x = 1;
    int y = 1;
    int z = 2;
    int w = 3;
    int v = 4;
    int q = 5;

    cout << largest6(x, y,z,w, v, q) << endl;

    // cout << largest(x, y, z) << endl;

    // x=1;
    // y=2;
    // z=3;

    // cout << largest(x, y, z) << endl;

    // x=3;
    // y=2;
    // z=1;

    // cout << largest(x, y, z) << endl;

    // x=2;
    // y=2;
    // z=1;

    // cout << largest(x, y, z) << endl;

    // int array[3] = {1, 2, 3};

    // cout << largestArrElement(array, 3) << endl;

    // int array2[3] = {2,5,2};

    // cout << largestArrElement(array2, 3) << endl;
}