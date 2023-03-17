#include "CountCars.h"

int CountCars::getCars(int colour)
{
    int numCars = 0;

    for (int i = 0; i < Showroom::numCars; i++)
    {
        if (cars[i].getColour() == colour)
        {
            numCars++;
        }
    }

    return numCars;
}

int CountCars::countVehicles(float threshold)
{
    int num = 0;

    for (int i = 0; i < Showroom::numCars; i++)
    {
        if (cars[i].getPrice() >= threshold)
        {
            num++;
        }

        if (lorries[i].getPrice() >= threshold)
        {
            num++;
        }
    }

    return num;
}