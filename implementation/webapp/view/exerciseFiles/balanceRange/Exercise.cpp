#include "Exercise.h"

float Exercise::getBalanceRange()
{
	float largest = 0;

	float smallest = 0;

	for (int i = 0; i < Exercise::numAccounts; i++)
	{
		if (accounts[i].getBalance() > largest)
		{
			largest = accounts[i].getBalance();
		}

		if (accounts[i].getBalance() < smallest)
		{
			smallest = accounts[i].getBalance();
		}
	}

	return smallest - largest;
}