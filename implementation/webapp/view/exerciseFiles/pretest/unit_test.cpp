#include "Exercise.h"
#include "gtest/gtest.h"

namespace {
    TEST(average, test0)
    {
        Exercise exercise;

        float a = 2;
        float b = 5;

        EXPECT_EQ(3.5, exercise.average(a, b));
    }

    TEST(average, test1)
    {
        Exercise exercise;

        float a = 5;
        float b = 2;

        EXPECT_EQ(3.5, exercise.average(a, b));
    }

    TEST(average, test2)
    {
        Exercise exercise;

        float a = 5;
        float b = 5;

        EXPECT_EQ(5, exercise.average(a, b));
    }

    TEST(totalArray, test0)
    {
        Exercise exercise;

        const int arraySize = 5;

        int array[arraySize] = {4, -1, 5, 3, 2};

        EXPECT_EQ(13, exercise.totalArray(array, arraySize));
    }

    TEST(swapValues, test0)
    {
        Exercise exercise;

        int a = 5;
        int b = 2;

        exercise.swapValues(a, b);

        EXPECT_TRUE(a == 2 && b == 5);
    }

    TEST(incrementValue, test0)
    {
        Exercise exercise;

        int a = 5;

        exercise.incrementValue(a);

        EXPECT_EQ(6, a);
    }

}