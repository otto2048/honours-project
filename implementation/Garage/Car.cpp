#include "Car.h"

Car::Car()
{
	id = -1;
	manufacturer = 0;
	colour = 0;
	mpg = 0;
	fuelCap = 0;
	price = 0;

	range = getRange();

	numDoors = 4;
}

Car::Car(int id_, int manufacturer_, int colour_, float mpg_, float fuelCap_, float price_, int year_, string reg_, int numDoors_) : Vehicle(id_, manufacturer_, colour_, mpg_, fuelCap_, price_, year_, reg_)
{
	numDoors = numDoors_;
}

int Car::getNumDoors()
{
	return numDoors;
}