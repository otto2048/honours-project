#include "Showroom.h"

Showroom::Showroom()
{
	//create random cars and lorries
	for (int i = 0; i < numCars; i++)
	{
		cars[i] = Car(i, rand() % Vehicle::numManufacturers, rand() % Vehicle::numColours, (rand() % 20) + 40, (rand() % 10) + 50, (rand() % 1000) + 4000, 4);
	}
}

void Showroom::printVehicles()
{
	cout << "Vehicle ID   Vehicle Type   Price";
	cout << endl;

	for (int i = 0; i < numCars; i++)
	{
		cout << "     " << cars[i].getId() << "     |     Car    |    " << cars[i].getPrice() << endl;
	}
}