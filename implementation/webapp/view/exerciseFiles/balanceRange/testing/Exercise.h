#pragma once
#include "Bank.h"

class My_Bank : public Bank
{
public:
    My_Bank(char);
    My_Bank();
    
	float getBalanceRange();
};

