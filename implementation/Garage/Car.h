#pragma once
#include "Vehicle.h"
class Car : public Vehicle
{
private:
	int numDoors;

public:
	Car();
	Car(int, string, int, float, float, float, int);
	
	int getNumDoors();
};

