#include "Showroom.h"

int main()
{
	srand(time(0)); //initialise random number generator with time

	Showroom showroom;
	showroom.printVehicles();

	return 0;
}