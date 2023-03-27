#pragma once
#include "Showroom.h"

class CountCars : public Showroom
{
public:
	int getCars(int);
	int countVehicles(float);
	int* countColours();

	Car* filterCars(int);
};

