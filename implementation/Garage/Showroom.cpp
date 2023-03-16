#include "Showroom.h"

Showroom::Showroom()
{
	//create random cars and lorries
	for (int i = 0; i < numCars; i++)
	{
		cars[i] = Car(i, rand() % Vehicle::numManufacturers, rand() % Vehicle::numColours, (rand() % 20) + 40, (rand() % 10) + 50, (rand() % 1000) + 4000, 4);
	}

	for (int i = 0; i < numLorries; i++)
	{
		lorries[i] = Lorry(i, rand() % Vehicle::numManufacturers, rand() % Vehicle::numColours, (rand() % 20) + 40, (rand() % 10) + 50, (rand() % 1000) + 4000, 8, (rand() % 1000) + 2000);
	}
}

void Showroom::printVehicles()
{
	cout << "Vehicle ID   Vehicle Type   Manufacturer";
	cout << endl;

	for (int i = 0; i < numCars; i++)
	{
		cout << "     " << cars[i].getId() << "     |     Car    |    " << cars[i].getManufacturer() << endl;
	}

	for (int i = 0; i < numLorries; i++)
	{
		cout << "     " << lorries[i].getId() << "     |   Lorry    |    " << lorries[i].getManufacturer() << endl;
	}
}