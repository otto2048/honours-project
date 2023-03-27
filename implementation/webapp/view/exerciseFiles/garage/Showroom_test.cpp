#include "Showroom.h"

Showroom::Showroom()
{
	//create cars
    cars[0] = Car(0, 0, 0, 45, 50, 0, 2001, "", 4);
    cars[1] = Car(1, 0, 0, 45, 50, 1, 2002, "", 4);
    cars[2] = Car(2, 0, 0, 45, 50, 2, 2003, "", 4);
    cars[3] = Car(3, 0, 0, 45, 50, 3, 2004, "", 4);
    cars[4] = Car(4, 0, 0, 45, 50, 4, 2005, "", 4);
    cars[5] = Car(5, 0, 0, 45, 50, 5, 2006, "", 4);
    cars[6] = Car(6, 0, 0, 45, 50, 6, 2007, "", 4);
    cars[7] = Car(7, 0, 0, 45, 50, 7, 2008, "", 4);
    cars[8] = Car(8, 0, 0, 45, 50, 8, 2009, "", 4);
    cars[9] = Car(9, 0, 0, 45, 50, 9, 2010, "", 4);
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