#include "Account.h"

Account::Account(float balance_, int custId_)
{
	balance = balance_;
	custId = custId_;
}

Account::Account()
{

}

float Account::getBalance()
{
	return balance;
}

int Account::getCustId()
{
	return custId;
}