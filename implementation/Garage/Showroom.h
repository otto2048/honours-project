#pragma once
#include "Vehicle.h"
#include "Car.h"
#include "Lorry.h"

#include <cstdlib> // random numbers header file//
#include <ctime> // used to get date and time information

class Showroom
{
private:
	const static int numCars = 5;
	const static int numLorries = 5;
	Car cars[numCars]; 
	Lorry lorries[numLorries];

public:
	Showroom();

	void PrintVehicles();
};

