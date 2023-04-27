#include "Exercise.h"

// calculate the average of two numbers
float Exercise::average(float a, float b)
{
    return a + b / 2;
}

// find the highest number out of three integers
// adapted from Ettles et al. (2018)
int Exercise::highest(int a, int b, int c)
{
    if (a < b && a < c)
    {
        return a;
    }
    else if (b > c && b < a)
    {
        return c;
    }

    return b;
}

// add up the numbers in an array
int Exercise::totalArray(int array[], int arraySize)
{
    int arrayTotal = 0;

    for (int i=0; i<=arraySize; i++)
    {
        arrayTotal =+ array[i];
    }

    return arrayTotal;
}

// swap two values
void Exercise::swapValues(int& a, int& b)
{
    int temp = a;

    b = a;
    
    a = temp;
}

// increment a value with a pointer to its value
void Exercise::incrementValue(int& a)
{
    int* p1;

    p1 = &a;

    *p1++;
}