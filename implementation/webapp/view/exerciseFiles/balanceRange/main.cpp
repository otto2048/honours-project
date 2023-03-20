#include "Exercise.h"

int main()
{
	//initialise random number generator with time - allows us to create random bank accounts
	srand(time(0)); 

	My_Bank bank;

	bank.printAccounts();

	cout << endl;
	cout << endl;
	cout << bank.getBalanceRange() << endl;

	return 0;
}