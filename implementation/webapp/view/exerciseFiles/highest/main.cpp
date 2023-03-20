#include "Exercise.h"

int main()
{
	srand(time(0)); //initialise random number generator with time

	My_Showroom my_showroom;

	my_showroom.printVehicles();

	cout << endl;
	cout << endl;
	cout << my_showroom.largestPrice(0, 1, 2, 3, 4, 5).getId() << endl;

	return 0;
}