#include "Exercise.h"
#include "gtest/gtest.h"

namespace {
	TEST(largestPrice, test1)
	{
        Exercise garage;

		EXPECT_EQ(5, garage.largestPrice(5, 5, 1, 2, 4, 1).getId());
	}
}