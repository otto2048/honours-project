#include "ExerciseOne.h"

int ExerciseOne::getCarPriceRange()
{
    int largest = cars[0].getPrice();
    //int largest = 0;
    int smallest = cars[0].getPrice();
    //int smallest = 0;

    for (int i = 0; i < Showroom::numCars; i++)
    {
        if (cars[i].getPrice() > largest)
        {
            largest = cars[i].getPrice();
        }

        if (cars[i].getPrice() < smallest)
        {
            smallest = cars[i].getPrice();
        }
    }

    return largest - smallest;
}

int ExerciseOne::boxesNeeded(int apples)
{
    int remainder = apples % 20;

    if (remainder == 0)
    {
        return apples / 20;
    }

    return apples / 20 + 1;
}

bool ExerciseOne::goodDinner(int pizzas, bool weekend)
{
    if (!weekend)
    {
        if (pizzas >= 10 && pizzas <= 20)
        {
            return true;
        }
    }
    else
    {
        if (pizzas >= 10)
        {
            return true;
        }
    }

    return false;
}

// count the number of cars going on sale
// cars going on sale must be between min and max price (inclusive)
//      unless its the weekend then there is no upper bound to the cars going
//      on sale
int ExerciseOne::carsOnSale(float minPrice, float maxPrice, bool weekend)
{
    int ret = 0;

    for (int i = 0; i < Showroom::numCars; i++)
    {
        Car currentCar = cars[i];

        if (!weekend)
        {
            if (currentCar.getPrice() >= minPrice && currentCar.getPrice() <= maxPrice)
            {
                ret++;
            }
        }
        else
        {
            if (currentCar.getPrice() >= minPrice)
            {
                ret++;
            }
        }
    }

    return ret;
}

Car ExerciseOne::mostExpensiveCar(Car& a, Car& b, Car& c)
{
    if (a.getPrice() >= b.getPrice() && a.getPrice() >= c.getPrice())
    {
        return a;
    }
    else if (b.getPrice() >= a.getPrice() && b.getPrice() >= c.getPrice())
    {
        return b;
    }

    return c;
}

Car ExerciseOne::mostExpensiveCar(int a, int b, int c, int d, int e, int f)
{
    Car one = mostExpensiveCar(cars[a], cars[b], cars[c]);

    Car two = mostExpensiveCar(cars[d], cars[e], cars[f]);

    return mostExpensiveCar(one, two);
}

Car ExerciseOne::mostExpensiveCar(Car& a, Car& b)
{
    if (a.getPrice() > b.getPrice())
    {
        return a;
    }

    return b;
}