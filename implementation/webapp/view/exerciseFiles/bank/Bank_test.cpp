#include "Bank.h"

Bank::Bank()
{
	//create accounts
    accounts[0] = Account(593, 0);
    accounts[1] = Account(517, 1);
    accounts[2] = Account(565, 2);
    accounts[3] = Account(502, 3);
    accounts[4] = Account(562, 4);
    accounts[5] = Account(500, 5);
    accounts[6] = Account(-470, 6);
    accounts[7] = Account(-480, 7);
    accounts[8] = Account(-489, 8);
    accounts[9] = Account(-430, 9);
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