#pragma once
#include "Vehicle.h"
#include "Car.h"
#include "Lorry.h"

#include <cstdlib> // random numbers header file//
#include <ctime> // used to get date and time information

class Showroom
{
private:
	int numCars;
	int numLorries;
	Car* cars; 
	Lorry* lorries;

public:
	Showroom(int, int);
	~Showroom();

	void PrintVehicles();
};

