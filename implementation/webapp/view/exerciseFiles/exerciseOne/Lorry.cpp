#include "Lorry.h"

Lorry::Lorry()
{
	id = -1;
	manufacturer = 0;
	colour = 0;
	mpg = 0;
	fuelCap = 0;
	price = 0;

	range = getRange();

	wheels = 4;
	haulage = 0;
}

Lorry::Lorry(int id_, int manufacturer_, int colour_, float mpg_, float fuelCap_, float price_, int wheels_, float haulage_)
{
	id = id_;
	manufacturer = manufacturer_;
	colour = colour_;
	mpg = mpg_;
	fuelCap = fuelCap_;
	price = price_;

	range = getRange();

	wheels = wheels_;
	haulage = haulage_;
}

float Lorry::getHaulage()
{
	return haulage;
}

int Lorry::getWheels()
{
	return wheels;
}