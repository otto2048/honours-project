#include "Bank.h"

Bank::Bank()
{
	//create some random accounts
	for (int i = 0; i < numAccounts; i++)
	{
		bool overdrawn = rand() > (RAND_MAX / 2);

		if (overdrawn)
		{
			accounts[i] = Account((rand() % 100) - 500, i);
		}
		else
		{
			accounts[i] = Account((rand() % 100) + 500, i);
		}
	}
}

//output accounts information
void Bank::printAccounts()
{
	cout << "Customer ID   Balance" << endl;

	for (int i = 0; i < numAccounts; i++)
	{
		cout << "      " << accounts[i].getCustId() << "     |";
			
		if (accounts[i].getBalance() >= 0)
		{
			cout << "   ";
		}
		else
		{
			cout << "  ";
		}
		
		cout << accounts[i].getBalance() << endl;
	}
}