#include "Exercise.h"

int main()
{
	//initialise random number generator with time - allows us to create random vehicles
	srand(time(0));

	My_Showroom my_showroom;

	my_showroom.printVehicles();

	cout << endl;
	cout << endl;
	cout << my_showroom.carsOnSale(4200, 4600, true) << endl;

	return 0;
}