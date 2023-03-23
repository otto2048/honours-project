#pragma once
#include "SavingAccount.h"
class My_SavingAccount :
    public SavingAccount
{
public:
    My_SavingAccount(float, int, float, bool);
    void withdraw(float);
};

