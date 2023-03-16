#pragma once
#include "Vehicle.h"
class Car : public Vehicle
{
private:
	int numDoors;

public:
	Car();
	Car(int, int, int, float, float, float, int);
	
	int getNumDoors();
};

