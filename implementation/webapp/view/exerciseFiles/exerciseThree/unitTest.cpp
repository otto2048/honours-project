#include "exerciseThree.h"
#include "gtest/gtest.h"

namespace {
	TEST(testFactors, test1)
	{
		EXPECT_EQ(true, testFactors(10, 5, 2));
	}

    TEST(testFactors, test2)
	{
		EXPECT_EQ(false, testFactors(10, 3, 5));
	}

    TEST(testFactors, test3)
	{
		EXPECT_EQ(false, testFactors(10, 3, 9));
	}
}