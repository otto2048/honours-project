#include "Exercise.h"

int main()
{
	srand(time(0)); //initialise random number generator with time

	My_Showroom garage;

	garage.printVehicles();

	cout << endl;
	cout << endl;
	cout << garage.carsOnSale(4200, 4600, true) << endl;

	return 0;
}