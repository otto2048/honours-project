#pragma once
class Account
{
protected:
	float balance;
	int custId;

public:
	Account(float, int);
	Account();

	float getBalance();
	int getCustId();
};

