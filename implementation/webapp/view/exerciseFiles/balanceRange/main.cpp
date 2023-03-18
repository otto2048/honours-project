#include "Exercise.h"

int main()
{
	srand(time(0)); //initialise random number generator with time

	Exercise bank;

	bank.printAccounts();

	cout << endl;
	cout << endl;
	cout << bank.getBalanceRange() << endl;
	cout << bank.getBalanceRangeErrors() << endl;

	return 0;
}