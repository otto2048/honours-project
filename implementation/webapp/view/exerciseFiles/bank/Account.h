#pragma once
class Account
{
private:
	float balance;
	int custId;

public:
	Account(float, int);
	Account();

	//getters
	float getBalance();
	int getCustId();
};

