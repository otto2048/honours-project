#include "Exercise.h"

float Exercise::getBalanceRange()
{
	float largest = accounts[0].getBalance();

	float smallest = accounts[0].getBalance();

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

	return largest - smallest;
}

// possible inputs: all positive numbers, all negative numbers, mixed positive and negative numbers
float Exercise::getBalanceRangeErrors()
{
	float largest = 0;
	float smallest = 0;

	for (int i = 0; i < Exercise::numAccounts; i++)
	{
		Account currentAccount = accounts[i];

		if (currentAccount.getBalance() > largest)
		{
			largest = accounts[i].getBalance();
		}

		if (currentAccount.getBalance() < smallest)
		{
			smallest = accounts[i].getBalance();
		}
	}

	return smallest - largest;
}