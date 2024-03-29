#include "Exercise.h"
#include "gtest/gtest.h"
#include <algorithm>
#include <iterator>

namespace {

    TEST(testFactors, test0)
    {
        Exercise exercise;

        EXPECT_EQ(false, exercise.testFactors(10, 3, 9));
    }

    TEST(testFactors, test1)
    {
        Exercise exercise;

        EXPECT_EQ(false, exercise.testFactors(10, 3, 5));
    }

    TEST(testFactors, test2)
    {
        Exercise exercise;

        EXPECT_EQ(true, exercise.testFactors(10, 5, 2));
    }

    TEST(sumIsEven, test0)
    {
        Exercise exercise;

        EXPECT_EQ(false, exercise.sumIsEven(5, 2));
    }

    TEST(sumIsEven, test1)
    {
        Exercise exercise;

        EXPECT_EQ(true, exercise.sumIsEven(5, 3));
    }

    TEST(range, test0)
    {
        Exercise exercise;

        const int arraySize = 5;

        int array[arraySize] = {4, -1, 5, 3, 2};

        EXPECT_EQ(6, exercise.range(array, arraySize));
    }

    TEST(range, test1)
    {
        Exercise exercise;

        const int arraySize = 5;

        int array[arraySize] = {-1, -2, -3, -6, -7};

        EXPECT_EQ(6, exercise.range(array, arraySize));
    }

    TEST(range, test2)
    {
        Exercise exercise;

        const int arraySize = 5;

        int array[arraySize] = {1, 2, 3, 6, 7};

        EXPECT_EQ(6, exercise.range(array, arraySize));
    }

    TEST(goodDinner, test0)
    {
        Exercise exercise;

        EXPECT_EQ(false, exercise.goodDinner(9, false));
    }

    TEST(goodDinner, test1)
    {
        Exercise exercise;

        EXPECT_EQ(true, exercise.goodDinner(15, false));
    }

    TEST(goodDinner, test2)
    {
        Exercise exercise;

        EXPECT_EQ(false, exercise.goodDinner(21, false));
    }

    TEST(goodDinner, test3)
    {
        Exercise exercise;

        EXPECT_EQ(false, exercise.goodDinner(9, true));
    }

    TEST(goodDinner, test4)
    {
        Exercise exercise;

        EXPECT_EQ(true, exercise.goodDinner(15, true));
    }

    TEST(goodDinner, test5)
    {
        Exercise exercise;

        EXPECT_EQ(true, exercise.goodDinner(21, true));
    }

    TEST(filterData, test0)
    {
        int array[14] = {0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13};

        int correctArray[14] = {-1, 1, 2, -1, 4, 5, -1, 7, 8, -1, 10, 11, -1, -1};

        Exercise exercise;

        exercise.filterData(3, -1, array, 14);

        EXPECT_TRUE(std::equal(std::begin(array), std::end(array), std::begin(correctArray), std::end(correctArray)));
    }
}