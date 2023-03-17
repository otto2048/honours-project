#include "ExerciseOne.h"
#include "CountCars.h"

int main()
{
	srand(time(0)); //initialise random number generator with time

	ExerciseOne exerciseOne;

	exerciseOne.printVehicles();

	cout << endl;
	cout << endl;
	cout << exerciseOne.carsOnSale(4200, 4600, true) << endl;
	cout << exerciseOne.carsOnSale(4200, 4600, false) << endl;

	/*CountCars countCars;

	countCars.printVehicles();

	cout << countCars.countVehicles(4500) << endl;*/

	return 0;
}