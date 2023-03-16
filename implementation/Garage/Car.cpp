#include "Car.h"

Car::Car()
{
	id = -1;
	manufacturer = "";
	colour = 0;
	mpg = 0;
	fuelCap = 0;
	price = 0;

	range = getRange();

	numDoors = 4;
}

Car::Car(int id_, string manufacturer_, int colour_, float mpg_, float fuelCap_, float price_, int numDoors_)
{
	id = id_;
	manufacturer = manufacturer_;
	colour = colour_;
	mpg = mpg_;
	fuelCap = fuelCap_;
	price = price_;

	range = getRange();

	numDoors = numDoors_;
}

int Car::getNumDoors()
{
	return numDoors;
}