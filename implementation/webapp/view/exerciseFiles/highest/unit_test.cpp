#include "Exercise.h"
#include "gtest/gtest.h"

namespace {
	TEST(largestPrice, test1)
	{
        My_Showroom garage;

		EXPECT_EQ(9, garage.largestPrice(9, 9, 7, 2, 4, 1).getId());
	}

	TEST(largestPrice, test2)
	{
        My_Showroom garage;

		EXPECT_EQ(9, garage.largestPrice(7, 9, 9, 2, 4, 1).getId());
	}

	TEST(largestPrice, test3)
	{
        My_Showroom garage;

		EXPECT_EQ(9, garage.largestPrice(9, 7, 9, 2, 4, 1).getId());
	}

	TEST(largestPrice, test4)
	{
        My_Showroom garage;

		EXPECT_EQ(9, garage.largestPrice(9, 9, 9, 2, 4, 1).getId());
	}

	TEST(largestPrice, test5)
	{
        My_Showroom garage;

		EXPECT_EQ(9, garage.largestPrice(9, 8, 7, 2, 4, 1).getId());
	}

	TEST(largestPrice, test6)
	{
        My_Showroom garage;

		EXPECT_EQ(9, garage.largestPrice(8, 9, 7, 2, 4, 1).getId());
	}

	TEST(largestPrice, test7)
	{
        My_Showroom garage;

		EXPECT_EQ(9, garage.largestPrice(7, 8, 9, 2, 4, 1).getId());
	}
}