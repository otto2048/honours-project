#include "Showroom.h"

Showroom::Showroom(int numCars_, int numLorries_)
{
	numCars = numCars_;
	numLorries = numLorries_;

	cars = new Car[numCars]();
	lorries = new Lorry[numLorries]();

	//create random cars and lorries
	for (int i = 0; i < numCars; i++)
	{
		
	}
}

Showroom::~Showroom()
{
	if (cars)
	{
		delete[] cars;
	}

	if (lorries)
	{
		delete[] lorries;
	}
}

void Showroom::PrintVehicles()
{
}