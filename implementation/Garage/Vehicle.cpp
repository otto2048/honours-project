#include "Vehicle.h"

Vehicle::Vehicle()
{
	id = -1;
	manufacturer = "";
	colour = 0;
	mpg = 0;
	fuelCap = 0;
	price = 0;

	range = getRange();
}

Vehicle::Vehicle(int id_, string manufacturer_, int colour_, float mpg_, float fuelCap_, float price_)
{
	id = id_;
	manufacturer = manufacturer_;
	colour = colour_;
	mpg = mpg_;
	fuelCap = fuelCap_;
	price = price_;

	range = getRange();
}

int Vehicle::getId()
{
	return id;
}

string Vehicle::getRegNum()
{
	return regNum;
}

string Vehicle::getManufacturer()
{
	return manufacturer;
}

float Vehicle::getPrice()
{
	return price;
}

string Vehicle::getColour()
{
	return colours[colour];
}

float Vehicle::getMpg()
{
	return mpg;
}

float Vehicle::getRange()
{
	return fuelCap * mpg;
}

float Vehicle::getFuelCap()
{
	return fuelCap;
}