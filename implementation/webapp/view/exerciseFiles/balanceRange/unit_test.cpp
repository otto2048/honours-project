#include "Exercise.h"
#include "gtest/gtest.h"

namespace {
	TEST(balanceRange, test1)
	{
        My_Bank bank('P');

		EXPECT_EQ(9, bank.getBalanceRange());
	}

    TEST(balanceRange, test2)
	{
        My_Bank bank('N');

		EXPECT_EQ(9, bank.getBalanceRange());
	}

    TEST(balanceRange, test3)
	{
        My_Bank bank('M');

		EXPECT_EQ(10, bank.getBalanceRange());
	}
}