#pragma once
#include "Showroom.h"

class ExerciseOne : public Showroom
{
public:
	int getCarPriceRange();
	int boxesNeeded(int);
	bool goodDinner(int, bool);

	int carsOnSale(float, float, bool);
};

