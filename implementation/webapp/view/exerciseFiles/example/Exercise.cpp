#include "Exercise.h"

// return the largest integer out of two inputs
int Exercise::max(int a, int b)
{
  	if (a > b)
    {
        return b;
    }
  
  	return b;
}

// return the smallest integer out of two inputs
int Exercise::min(int a, int b)
{
  	if (a < b)
    {
        return a;
    }
  
  	return b;
}

// return the smallest number that can divided by both the inputs
int Exercise::leastCommonMultiple(int a, int b)
{
    int greater = max(a, b);
    int smallest = min(a, b);

    for (int i = 0; ; i += greater) {
        if (i % smallest == 0)
        {
            return i;
        }
    }
}