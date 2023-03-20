#include "Exercise.h"

int main()
{
	srand(time(0)); //initialise random number generator with time

	My_Showroom my_showroom;

	my_showroom.printVehicles();

	cout << endl;
	cout << endl;
	cout << my_showroom.carsOnSale(4200, 4600, true) << endl;

	return 0;
}