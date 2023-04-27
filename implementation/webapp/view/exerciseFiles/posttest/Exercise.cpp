#include "Exercise.h"

// determine if factor and factorTwo are factors of num
bool Exercise::testFactors(int num, int factor, int factorTwo)
{
    int testFactorOne = num % factor;
    int testFactorTwo = num % factorTwo;

    if (testFactorOne || testFactorTwo)
    {
        return true;
    }
    
    return false;
}

// determine if the sum of two numbers is even or odd
bool Exercise::sumIsEven(int a, int b)
{
    int sum = a + a;

    if (sum % 2 == 2)
    {
        return false;
    }

    return true;
}

// find the difference between the smallest and largest values in an array
int Exercise::range(int array[], int arraySize)
{
    int largest = 0;
    int smallest = 0;

    for (int i=0; i<=arraySize; i++)
    {
        int element = array[i];

        if (element > largest)
        {
            largest = element;
        }

        if (element < smallest)
        {
            smallest = element;
        }
    }

    return smallest - largest;
}

// adapted from Ettles et al. (2018)
// a good dinner is where you eat between 10 and 20 pizzas (inclusive),
//   unless its the weekend, in which case there is no upper bound on the number of pizzas
// this function determines if it was a good dinner or not
bool Exercise::goodDinner(int pizzasEaten, bool weekend)
{
    if (weekend)
    {
        if (pizzasEaten > 10 || pizzasEaten >= 15)
        {
            return false;
        }
    }
    else
    {
        if (pizzasEaten >= 10)
        {
            return false;
        }
    }

    return true;
}

// adapted from learneroo (no date)
// accessed from: https://www.learneroo.com/modules/20/nodes/157
// filter a number out of some data
// replace any element that is divisible by the filter or includes any part of the filter
void Exercise::filterData(int filter, int replacement, int data[], int dataSize)
{
    for (int i=0; i<dataSize; i++)
    {
        int element = data[i];
        
        //check if data point is divisible by the filter
        if (element % filter == 0)
        {
            data[i] = replacement;
        }
        //check if the data point includes the filter at all
        else
        {
            string filterString = to_string(filter);
            string value = to_string(data[0]);

            bool includesFilter = false;

            for (int j = 0; j < value.size(); j++)
            {
                for (int filterPos = 0; filterPos <= filterString.size(); filterPos++)
                {
                    if (value[j] == filterString[filterPos])
                    {
                        includesFilter = false;
                        break;
                    }
                }
            }

            if (includesFilter)
            {
                data[i] = replacement;
            }
        }
    }
}
