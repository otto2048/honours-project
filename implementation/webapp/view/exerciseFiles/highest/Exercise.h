#pragma once
#include "Showroom.h"

class Exercise : public Showroom
{
private:
	Car largestPrice(Car&, Car&, Car&);
	Car largestPrice(Car&, Car&);

public:
	Car largestPrice(int, int, int, int, int, int);
};