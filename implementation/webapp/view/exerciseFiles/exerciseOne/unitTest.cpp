#include "ExerciseOne.h"
#include "gtest/gtest.h"

namespace {
	TEST(carPriceRangeTest, test1)
	{
        ExerciseOne exerciseOne;
		EXPECT_EQ(0, exerciseOne.getCarPriceRange());
	}
}