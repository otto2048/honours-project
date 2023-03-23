#include "SavingAccount.h"

SavingAccount::SavingAccount(float balance_, int custId_, float allowance_, bool flexible_) : Account(balance_, custId_) {
	allowance = allowance_ - balance;
	flexible = flexible_;

	withdrawalCount = 0;
}

int SavingAccount::getWithdrawalCount()
{
	return withdrawalCount;
}

int SavingAccount::getAllowance()
{
	return allowance;
}

bool SavingAccount::getFlexible()
{
	return flexible;
}