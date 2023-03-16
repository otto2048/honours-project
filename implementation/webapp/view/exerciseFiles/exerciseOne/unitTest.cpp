#include "ExerciseOne.h"
#include "gtest/gtest.h"

namespace {
	TEST(carPriceRangeTest, test1)
	{
        ExerciseOne ExerciseOne;
		EXPECT_EQ(50, ExerciseOne.getCarPriceRange());
	}
}