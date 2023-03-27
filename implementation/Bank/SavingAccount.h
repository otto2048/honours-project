#pragma once
#include "Account.h"
class SavingAccount :
    public Account
{
protected:
    int withdrawalCount;
    float allowance;
    bool flexible;

public:
    SavingAccount(float, int, float, bool);

    int getWithdrawalCount();
    int getAllowance();
    bool getFlexible();
};

