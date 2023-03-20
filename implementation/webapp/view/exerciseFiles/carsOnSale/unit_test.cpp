#include "Exercise.h"
#include "gtest/gtest.h"

namespace {
	TEST(carsOnSale, test1)
	{
        Exercise garage;

		EXPECT_EQ(8, garage.carsOnSale(2, 5, true));
	}

    TEST(carsOnSale, test2)
	{
        Exercise garage;

		EXPECT_EQ(4, garage.carsOnSale(2, 5, false));
	}

	TEST(carsOnSale, test3)
	{
        Exercise garage;

		EXPECT_EQ(5, garage.carsOnSale(5, 5, true));
	}

    TEST(carsOnSale, test4)
	{
        Exercise garage;

		EXPECT_EQ(1, garage.carsOnSale(5, 5, false));
	}
}