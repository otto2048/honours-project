#include "Exercise.h"

int main()
{
	//initialise random number generator with time - allows us to create random vehicles
	srand(time(0));

	My_Showroom my_showroom;

	my_showroom.printVehicles();

	cout << endl;
	cout << endl;
	cout << my_showroom.largestPrice(0, 1, 2, 3, 4, 5).getId() << endl;

	return 0;
}