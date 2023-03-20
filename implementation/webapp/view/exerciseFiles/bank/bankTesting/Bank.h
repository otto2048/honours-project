#pragma once
#include <cstdlib> // random numbers header file//
#include <ctime> // used to get date and time information

#include "Account.h"

#include <iostream>

using std::cout;
using std::endl;

class Bank
{
public:
	const static int numAccounts = 10;

protected:
	Account accounts[numAccounts];

public:
	Bank();
    Bank(char);

	void printAccounts();
};