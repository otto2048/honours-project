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