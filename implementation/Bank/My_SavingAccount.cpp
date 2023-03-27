#include "My_SavingAccount.h"

My_SavingAccount::My_SavingAccount(float balance_, int custId_, float allowance_, bool flexible_) : SavingAccount(balance_, custId_, allowance_, flexible_) {
}

void My_SavingAccount::withdraw(float amount)
{
	balance = balance - amount;

	if (flexible)
	{
		allowance = allowance + amount;
	}
}