#include "Exercise.h"
#include "gtest/gtest.h"

namespace {
	TEST(largestPrice, test1)
	{
        Exercise garage;

		EXPECT_EQ(5, garage.largestPrice(5, 5, 1, 2, 4, 1).getId());
	}

	TEST(largestPrice, test2)
	{
        Exercise garage;

		EXPECT_EQ(5, garage.largestPrice(1, 5, 5, 2, 4, 1).getId());
	}

	TEST(largestPrice, test3)
	{
        Exercise garage;

		EXPECT_EQ(5, garage.largestPrice(5, 1, 5, 2, 4, 1).getId());
	}

	TEST(largestPrice, test4)
	{
        Exercise garage;

		EXPECT_EQ(5, garage.largestPrice(5, 5, 5, 2, 4, 1).getId());
	}

	TEST(largestPrice, test5)
	{
        Exercise garage;

		EXPECT_EQ(5, garage.largestPrice(5, 2, 1, 2, 4, 1).getId());
	}

	TEST(largestPrice, test6)
	{
        Exercise garage;

		EXPECT_EQ(5, garage.largestPrice(2, 5, 1, 2, 4, 1).getId());
	}

	TEST(largestPrice, test7)
	{
        Exercise garage;

		EXPECT_EQ(5, garage.largestPrice(1, 2, 5, 2, 4, 1).getId());
	}
}