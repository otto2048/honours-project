#include "Exercise.h"

int main()
{
	srand(time(0)); //initialise random number generator with time

	My_Bank bank;

	bank.printAccounts();

	cout << endl;
	cout << endl;
	cout << bank.getBalanceRange() << endl;

	return 0;
}