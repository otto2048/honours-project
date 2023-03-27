#include "Exercise.h"
#include "My_SavingAccount.h"
#include "NoSevens.h"

int main()
{
	srand(time(0)); //initialise random number generator with time

	Exercise bank('P');

	bank.printAccounts();

	cout << endl;
	cout << endl;
	cout << bank.getBalanceRange() << endl;
	cout << bank.getBalanceRangeErrors() << endl;
	cout << endl;
	cout << endl;

	My_SavingAccount savingsAccount(10000, 0, 20000, false);

	savingsAccount.withdraw(3000);

	cout << savingsAccount.getAllowance();
	cout << endl;
	cout << endl;

	NoSevens noSevens;

	noSevens.removeSevens(7, 1, 15);
}