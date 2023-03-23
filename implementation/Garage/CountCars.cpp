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

int* CountCars::countColours()
{
    static int colourCount[Car::numColours];

    for (int i = 0; i < Showroom::numCars; i++)
    {
        Car currentCar = cars[i];

        colourCount[currentCar.getColour()] = colourCount[currentCar.getColour()]++;
    }

    return colourCount;
}

//find all the cars where the year is divisible by the filter, or the year includes the filter
Car* CountCars::filterCars(int filter)
{
    static Car filteredCars[Showroom::numCars];

    for (int i = 0; i < Showroom::numCars; i++)
    {
        Car currentCar = cars[i];

        //if year is divisible by filter
        if (currentCar.getYear() % filter == 0)
        {
            filteredCars[i] = currentCar;
        }
        else
        {
            //check if the year includes the filter
            string yearString = std::to_string(currentCar.getYear());
            string filterString = std::to_string(filter);

            bool includesFilter = false;

            for (int j = 0; j < yearString.size(); j++)
            {
                for (int filterPos = 0; filterPos < filterString.size(); filterPos++)
                {
                    if (yearString[j] == filterString[filterPos])
                    {
                        includesFilter = true;
                        break;
                    }
                }
            }

            if (includesFilter)
            {
                filteredCars[i] = currentCar;
            }
            else
            {
                filteredCars[i] = Car();
            }
        }
    }

    return filteredCars;
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