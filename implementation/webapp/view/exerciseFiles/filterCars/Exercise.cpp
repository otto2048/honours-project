#include "Exercise.h"

//find all the cars where the year is divisible by the filter,
//      or the year includes any part of the filter
Car* My_Showroom::filterCars(int filter)
{
    static Car filteredCars[Showroom::numCars];

    for (int i = 0; i < Showroom::numCars; i++)
    {
        Car currentCar = cars[i];

        //if year is divisible by filter
        if (currentCar.getYear() % filter)
        {
            filteredCars[i] = currentCar;
        }
        else
        {
            //check if the year includes the filter
            string yearString = std::to_string(currentCar.getYear());
            string filterString = std::to_string(filter);

            bool includesFilter = true;

            for (int j = 0; j < yearString.size(); j++)
            {
                for (int filterPos = 0; filterPos < filterString.size(); filterPos++)
                {
                    if (yearString[j] == filterString[filterPos])
                    {
                        includesFilter = false;
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