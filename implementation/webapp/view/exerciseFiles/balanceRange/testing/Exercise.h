#pragma once
#include "Bank.h"

class Exercise : public Bank
{
public:
    Exercise(char);
    Exercise();
    
	float getBalanceRange();
};

