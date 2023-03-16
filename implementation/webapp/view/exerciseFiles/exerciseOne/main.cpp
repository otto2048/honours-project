#include "ExerciseOne.h"

int main()
{
	srand(time(0)); //initialise random number generator with time

	ExerciseOne exerciseOne;

	exerciseOne.printVehicles();

	cout << endl;
	cout << endl;

	cout << exerciseOne.getCarPriceRange() << endl;

	return 0;
}