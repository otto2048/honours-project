#include "logicalError.h"
#include "gtest/gtest.h"

namespace {
	TEST(largest6Test, test1)
	{
		EXPECT_EQ(5, largest6(5, 5, 1, 2, 4, 1));
	}
}