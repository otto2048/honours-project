#include "Exercise.h"
#include "gtest/gtest.h"

namespace {
	TEST(balanceRange, test1)
	{
        Exercise bank('P');

		EXPECT_EQ(9, bank.getBalanceRange());
	}

    TEST(balanceRange, test2)
	{
        Exercise bank('N');

		EXPECT_EQ(9, bank.getBalanceRange());
	}

    TEST(balanceRange, test3)
	{
        Exercise bank('M');

		EXPECT_EQ(10, bank.getBalanceRange());
	}
}