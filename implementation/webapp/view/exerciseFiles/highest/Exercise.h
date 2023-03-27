#pragma once
#include "Showroom.h"

//extend the Showroom class
class My_Showroom : public Showroom
{
private:
	Car largestPrice(Car&, Car&, Car&);
	Car largestPrice(Car&, Car&);

public:
	Car largestPrice(int, int, int, int, int, int);
};