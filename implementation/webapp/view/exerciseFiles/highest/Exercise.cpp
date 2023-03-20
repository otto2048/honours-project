#include "Exercise.h"

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

Car My_Showroom::largestPrice(int a, int b, int c, int d, int e, int f)
{
    Car one = largestPrice(cars[a], cars[b], cars[c]);

    Car two = largestPrice(cars[d], cars[e], cars[f]);

    return largestPrice(one, two);
}

Car My_Showroom::largestPrice(Car& a, Car& b)
{
    if (a.getPrice() > b.getPrice())
    {
        return a;
    }

    return b;
}