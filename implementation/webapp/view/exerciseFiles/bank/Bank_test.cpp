#include "Bank_test.h"

Bank::Bank()
{
	//create accounts
    accounts[0] = Account(5, 0);
    accounts[1] = Account(1, 1);
    accounts[2] = Account(2, 2);
    accounts[3] = Account(3, 3);
    accounts[4] = Account(4, 4);
    accounts[5] = Account(-1, 5);
    accounts[6] = Account(-2, 6);
    accounts[7] = Account(-3, 7);
    accounts[8] = Account(-4, 8);
    accounts[9] = Account(-5, 9);
}

Bank::Bank(char option)
{
	switch (option)
	{
	case 'P':
		//bank accounts all have positive balances
		accounts[0] = Account(5, 0);
		accounts[1] = Account(1, 1);
		accounts[2] = Account(2, 2);
		accounts[3] = Account(3, 3);
		accounts[4] = Account(4, 4);
		accounts[5] = Account(6, 5);
		accounts[6] = Account(7, 6);
		accounts[7] = Account(8, 7);
		accounts[8] = Account(9, 8);
		accounts[9] = Account(10, 9);

		break;
	case 'N':
		//bank accounts all have negative balances
		accounts[0] = Account(-5, 0);
		accounts[1] = Account(-1, 1);
		accounts[2] = Account(-2, 2);
		accounts[3] = Account(-3, 3);
		accounts[4] = Account(-4, 4);
		accounts[5] = Account(-6, 5);
		accounts[6] = Account(-7, 6);
		accounts[7] = Account(-8, 7);
		accounts[8] = Account(-9, 8);
		accounts[9] = Account(-10, 9);

		break;
	
	case 'M':
		//bank accounts have a mix of positive and negative balances
		accounts[0] = Account(5, 0);
		accounts[1] = Account(1, 1);
		accounts[2] = Account(2, 2);
		accounts[3] = Account(3, 3);
		accounts[4] = Account(4, 4);
		accounts[5] = Account(-1, 5);
		accounts[6] = Account(-2, 6);
		accounts[7] = Account(-3, 7);
		accounts[8] = Account(-4, 8);
		accounts[9] = Account(-5, 9);
		break;
	}
}

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