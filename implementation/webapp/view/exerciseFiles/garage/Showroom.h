#pragma once
#include "Vehicle.h"
#include "Car.h"

#include <cstdlib> // random numbers header file//
#include <ctime> // used to get date and time information

#include <iostream>

using std::cout;
using std::endl;

class Showroom
{
public:
	const static int numCars = 5;

protected:
	Car cars[numCars]; 

public:
	Showroom();

	void printVehicles();
};

