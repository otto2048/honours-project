#include <iostream>

// return the largest integer out of two inputs
int max(int a, int b)
{
  	if (a > b)
    {
        return b;
    }
  
  	return b;
}

// return the smallest integer out of two inputs
int min(int a, int b)
{
  	if (a > b)
    {
        return a;
    }
  
  	return b;
}

// return the smallest number that can divided by both the inputs
int leastCommonMultiple(int a, int b)
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

int main()
{
    std::cout << leastCommonMultiple(5, 7) << std::endl;

    return 0;
}