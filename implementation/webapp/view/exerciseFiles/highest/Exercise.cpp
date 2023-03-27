#include "Exercise.h"

// return the Car with the largest price out of three cars
Car My_Showroom::largestPrice(Car& a, Car& b, Car& c)
{
    if (a.getPrice() > b.getPrice() && a.getPrice() > c.getPrice())
    {
        return a;
    }
    else if (b.getPrice() > a.getPrice() && b.getPrice() > c.getPrice())
    {
        return b;
    }

    return c;
}

// return the Car with the largest price out of six cars within the Showroom
Car My_Showroom::largestPrice(int a, int b, int c, int d, int e, int f)
{
    // use largest 3 function
    Car one = largestPrice(cars[a], cars[b], cars[c]);

    // use largest 3 function
    Car two = largestPrice(cars[d], cars[e], cars[f]);

    return largestPrice(one, two);
}

// return the Car with the largest price out of two cars
Car My_Showroom::largestPrice(Car& a, Car& b)
{
    if (a.getPrice() > b.getPrice())
    {
        return a;
    }

    return b;
}