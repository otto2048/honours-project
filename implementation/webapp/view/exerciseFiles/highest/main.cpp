#include "Exercise.h"

int main()
{
	srand(time(0)); //initialise random number generator with time

	Exercise garage;

	garage.printVehicles();

	cout << endl;
	cout << endl;
	cout << garage.largestPrice(0, 1, 2, 3, 4, 5).getId() << endl;

	return 0;
}