#include "Exercise.h"

// count the number of cars going on sale
// cars going on sale must be between min and max price (inclusive)
//      unless its the weekend then there is no upper bound to the cars going
//      on sale
int Exercise::carsOnSale(float minPrice, float maxPrice, bool weekend)
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