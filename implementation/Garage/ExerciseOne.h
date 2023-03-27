#pragma once
#include "Showroom.h"

class ExerciseOne : public Showroom
{
private:
	Car mostExpensiveCar(Car&, Car&, Car&);
	Car mostExpensiveCar(Car&, Car&);

public:
	int getCarPriceRange();
	int boxesNeeded(int);
	bool goodDinner(int, bool);

	int carsOnSale(float, float, bool);

	Car mostExpensiveCar(int, int, int, int, int, int);
};

